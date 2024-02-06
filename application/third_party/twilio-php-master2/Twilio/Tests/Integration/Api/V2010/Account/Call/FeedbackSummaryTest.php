<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Api\V2010\Account\Call;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Serialize;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class FeedbackSummaryTest extends HolodeckTestCase
{
    public function testCreateRequest()
    {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                ->calls
                ->feedbackSummaries->create(new \DateTime('2008-01-02'), new \DateTime('2008-01-02'));
        } catch (DeserializeException $e) {
        } catch (TwilioException $e) {
        }

        $values = array(
            'StartDate' => Serialize::iso8601Date(new \DateTime('2008-01-02')),
            'EndDate' => Serialize::iso8601Date(new \DateTime('2008-01-02')),
        );

        $this->assertRequest(new Request(
            'post',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Calls/FeedbackSummary.json',
            null,
            $values
        ));
    }

    public function testCreateResponse()
    {
        $this->holodeck->mock(new Response(
            201,
            '
            {
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "call_count": 10200,
                "call_feedback_count": 729,
                "end_date": "2011-01-01",
                "include_subaccounts": false,
                "issues": [
                    {
                        "count": 45,
                        "description": "imperfect-audio",
                        "percentage_of_total_calls": "0.04%"
                    }
                ],
                "quality_score_average": 4.5,
                "quality_score_median": 4,
                "quality_score_standard_deviation": 1,
                "sid": "FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "start_date": "2011-01-01",
                "status": "completed",
                "date_created": "Tue, 31 Aug 2010 20:36:28 +0000",
                "date_updated": "Tue, 31 Aug 2010 20:36:44 +0000"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
            ->calls
            ->feedbackSummaries->create(new \DateTime('2008-01-02'), new \DateTime('2008-01-02'));

        $this->assertNotNull($actual);
    }

    public function testFetchRequest()
    {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                ->calls
                ->feedbackSummaries("FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {
        } catch (TwilioException $e) {
        }

        $this->assertRequest(new Request(
            'get',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Calls/FeedbackSummary/FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testFetchResponse()
    {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "call_count": 10200,
                "call_feedback_count": 729,
                "end_date": "2011-01-01",
                "include_subaccounts": false,
                "issues": [
                    {
                        "count": 45,
                        "description": "imperfect-audio",
                        "percentage_of_total_calls": "0.04%"
                    }
                ],
                "quality_score_average": 4.5,
                "quality_score_median": 4,
                "quality_score_standard_deviation": 1,
                "sid": "FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "start_date": "2011-01-01",
                "status": "completed",
                "date_created": "Tue, 31 Aug 2010 20:36:28 +0000",
                "date_updated": "Tue, 31 Aug 2010 20:36:44 +0000"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
            ->calls
            ->feedbackSummaries("FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testDeleteRequest()
    {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                ->calls
                ->feedbackSummaries("FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();
        } catch (DeserializeException $e) {
        } catch (TwilioException $e) {
        }

        $this->assertRequest(new Request(
            'delete',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Calls/FeedbackSummary/FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testDeleteResponse()
    {
        $this->holodeck->mock(new Response(
            204,
            null
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
            ->calls
            ->feedbackSummaries("FSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();

        $this->assertTrue($actual);
    }
}