<?php
/**
 * Neo Billing -  Accounting,  Invoicing  and CRM Software
 * Copyright (c) UltimateKode.com. All Rights Reserved
 * ***********************************************************************
 *
 *  Email: support@ultimatekode.com
 *  Website: https://www.ultimatekode.com
 *
 *  ************************************************************************
 *  * This software is furnished under a license and may be used and copied
 *  * only  in  accordance  with  the  terms  of such  license and with the
 *  * inclusion of the above copyright notice.
 *  * If you Purchased from Codecanyon, Please read the full License from
 *  * here- http://codecanyon.net/licenses/standard/
 * ***********************************************************************
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Tools_model extends CI_Model
{

    var $column_order = array('status', 'name', 'duedate', 'tdate', null);
    var $column_search = array('name', 'duedate', 'tdate');
    var $notecolumn_order = array(null, 'title', 'cdate', null);
    var $notecolumn_search = array('id', 'title', 'cdate');
    var $order = array('id' => 'asc');

    private function _task_datatables_query($cday = '')
    {

        $this->db->from('todolist');
        if ($cday) {
            $this->db->where('DATE(duedate)=', $cday);
        }


        $i = 0;

        foreach ($this->column_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) {
            $this->db->order_by($this->column_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function task_datatables($cday = '')
    {


        $this->_task_datatables_query($cday);

        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    function task_count_filtered($cday = '')
    {
        $this->_task_datatables_query($cday);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function task_count_all($cday = '')
    {
        $this->_task_datatables_query($cday);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function addtask($name, $status, $priority, $stdate, $tdate, $employee, $assign, $content)
    {

        $data = array('tdate' => date('Y-m-d H:i:s'), 'name' => $name, 'status' => $status, 'start' => $stdate, 'duedate' => $tdate, 'description' => $content, 'eid' => $employee, 'aid' => $assign, 'related' => 0, 'priority' => $priority, 'rid' => 0);
        return $this->db->insert('todolist', $data);
    }

    public function edittask($id, $name, $status, $priority, $stdate, $tdate, $employee, $content)
    {

        $data = array('tdate' => date('Y-m-d H:i:s'), 'name' => $name, 'status' => $status, 'start' => $stdate, 'duedate' => $tdate, 'description' => $content, 'eid' => $employee, 'related' => 0, 'priority' => $priority, 'rid' => 0);
        $this->db->set($data);
        $this->db->where('id', $id);
        return $this->db->update('todolist');
        //return $this->db->insert('todolist', $data);
    }

    public function settask($id, $stat)
    {

        $data = array('status' => $stat);
        $this->db->set($data);
        $this->db->where('id', $id);
        return $this->db->update('todolist');
    }

    public function deletetask($id)
    {

        return $this->db->delete('todolist', array('id' => $id));
    }

    public function viewtask($id)
    {

        $this->db->select('todolist.*,employee_profile.name AS emp, assi.name AS assign');
        $this->db->from('todolist');
        $this->db->where('todolist.id', $id);
        $this->db->join('employee_profile', 'employee_profile.id = todolist.eid', 'left');
        $this->db->join('employee_profile AS assi', 'assi.id = todolist.aid', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }


    public function task_stats()
    {

        $query = $this->db->query("SELECT
				COUNT(IF( status = 'Due', id, NULL)) AS Due,
				COUNT(IF( status = 'Progress', id, NULL)) AS Progress,
				COUNT(IF( status = 'Done', id, NULL)) AS Done
				FROM todolist ");

        echo json_encode($query->result_array());

    }

    //goals

    public function goals($id)
    {

        $this->db->select('*');
        $this->db->from('goals');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function setgoals($income, $expense, $sales, $netincome)
    {


        $data = array('income' => $income, 'expense' => $expense, 'sales' => $sales, 'netincome' => $netincome);
        $this->db->set($data);
        $this->db->where('id', 1);
        return $this->db->update('goals');
    }

    //notes

    private function _notes_datatables_query()
    {

        $this->db->from('notes');

        $i = 0;

        foreach ($this->notecolumn_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) {
            $this->db->order_by($this->notecolumn_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function notes_datatables()
    {
        $this->_notes_datatables_query();
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    function notes_count_filtered()
    {
        $this->_task_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function notes_count_all()
    {
        $this->_task_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }


    function addnote($title, $content)
    {
        $data = array('title' => $title, 'content' => $content, 'cdate' => date('Y-m-d'));
        return $this->db->insert('notes', $data);

    }

    public function note_v($id)
    {
        $this->db->select('*');
        $this->db->from('notes');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function deletenote($id)
    {
        return $this->db->delete('notes', array('id' => $id));

    }


    //documents list

    var $doccolumn_order = array(null, 'title', 'cdate', null);
    var $doccolumn_search = array('title', 'cdate');

    public function documentlist()
    {
        $this->db->select('*');
        $this->db->from('documents');
        $query = $this->db->get();
        return $query->result_array();
    }

    function adddocument($title, $filename)
    {
        $data = array('title' => $title, 'filename' => $filename, 'cdate' => date('Y-m-d'));
        return $this->db->insert('documents', $data);

    }

    function deletedocument($id)
    {
        $this->db->select('filename');
        $this->db->from('documents');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result = $query->row_array();
        if ($this->db->delete('documents', array('id' => $id))) {

            unlink(FCPATH . 'userfiles/documents/' . $result['filename']);
            return true;
        } else {
            return false;
        }

    }


    function document_datatables()
    {
        $this->document_datatables_query();
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    private function document_datatables_query()
    {

        $this->db->from('documents');

        $i = 0;

        foreach ($this->doccolumn_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->doccolumn_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) {
            $this->db->order_by($this->doccolumn_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function document_count_filtered()
    {
        $this->document_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function document_count_all()
    {
        $this->document_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function pending_tasks()
    {
        $this->db->select('*');
        $this->db->from('todolist');
        $this->db->where('status', 'Due');
        $this->db->order_by('DATE(duedate)', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function pending_tasks_user($id)
    {
        $this->db->select('*');
        $this->db->from('todolist');
        $this->db->where('status', 'Due');
        $this->db->where('eid', $id);
        $this->db->order_by('DATE(duedate)', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }


    public function editnote($id, $title, $content)
    {
        $data = array(
            'title' => $title,
            'content' => $content

        );


        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('notes')) {
            return true;
        } else {
            return false;
        }

    }


}
