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

class Emailinvoice Extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tools_model', 'tools');
        $this->load->model('templates_model', 'templates');
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $this->load->library('parser');

    }

    //todo section

    public function template()
    {

        $id = $this->input->post('invoiceid');
        $ttype = $this->input->post('ttype');
        $itype = $this->input->post('itype');

        switch ($ttype){
            case 'quote':

                $this->load->model('quote_model', 'quote');
                $invoice = $this->quote->quote_details($id);

                $validtoken = hash_hmac('ripemd160', 'q' . $invoice['tid'], $this->config->item('encryption_key'));

                $link = base_url('billing/quoteview?id=' . $invoice['tid'] . '&token=' . $validtoken);
                break;
            case 'purchase':
                $this->load->model('purchase_model', 'purchase');
                $invoice = $this->purchase->purchase_details($id);
                $validtoken = hash_hmac('ripemd160', 'p' . $invoice['tid'], $this->config->item('encryption_key'));

                $link = base_url('billing/purchase?id=' . $invoice['tid'] . '&token=' . $validtoken);
                break;
            case 'stock':
                $this->load->model('stockreturn_model', 'stockreturn');
                $invoice = $this->stockreturn->purchase_details($id);
                $validtoken = hash_hmac('ripemd160', 's' . $invoice['tid'], $this->config->item('encryption_key'));

                $link = base_url('billing/stockreturn?id=' . $invoice['tid'] . '&token=' . $validtoken);
                break;

            default :
                if ($itype == 'rec') {
                    $this->load->model('rec_invoices_model', 'invoices');
                    $invoice = $this->invoices->invoice_details($id);
                    $validtoken = hash_hmac('ripemd160', 'rec' . $invoice['tid'], $this->config->item('encryption_key'));
                    $link = base_url('billing/invoice?id=' . $invoice['tid'] . '&token=' . $validtoken);
                } else {
                    $this->load->model('invoices_model', 'invoices');
                    $invoice = $this->invoices->invoice_details($id);
                    $validtoken = hash_hmac('ripemd160', $invoice['tid'], $this->config->item('encryption_key'));
                    $link = base_url('billing/view?id=' . $invoice['tid'] . '&token=' . $validtoken);
                }
                break;


        }


        switch ($ttype) {
            case 'notification':
                $template = $this->templates->template_info(6);
                break;

            case 'reminder':
                $template = $this->templates->template_info(7);
                break;

            case 'refund':
                $template = $this->templates->template_info(8);
                break;


            case 'received':
                $template = $this->templates->template_info(9);
                break;

            case 'overdue':
                $template = $this->templates->template_info(10);
                break;


            case 'quote':
                $template = $this->templates->template_info(11);
                break;


            case 'purchase':
                $template = $this->templates->template_info(12);
                $invoice['multi']=0;
                break;
            case 'stock':
                $template = $this->templates->template_info(13);
                $invoice['multi']=0;
                break;


        }


        $data = array(
            'Company' => $this->config->item('ctitle'),
            'BillNumber' => $invoice['tid']
        );
        $subject= $this->parser->parse_string($template['key1'], $data, TRUE);


        $data = array(
            'Company' => $this->config->item('ctitle'),
            'BillNumber' => $invoice['tid'],
            'URL' => "<a href='$link'>$link</a>",
            'CompanyDetails' => '<h6><strong>' . $this->config->item('ctitle') . ',</strong></h6>
<address>' . $this->config->item('address') . '<br>' . $this->config->item('address2') . '</address>
             ' . $this->lang->line('Phone') . ' : ' . $this->config->item('phone') . '<br>  ' . $this->lang->line('Email') . ' : ' . $this->config->item('email'),
            'DueDate'=>dateformat($invoice['invoiceduedate']),
            'Amount'=>amountExchange($invoice['total'], $invoice['multi'])
        );
        $message = $this->parser->parse_string($template['other'], $data, TRUE);


        echo json_encode(array('subject' => $subject, 'message' => $message));
    }


}


