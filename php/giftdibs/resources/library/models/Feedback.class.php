<?php
class Feedback extends DatabaseObject {
	
	protected $tableName = "Feedback";
	
	protected $tableFields = array(
		"feedbackId",
		"feedbackReasonId",
		"requestFollowUp",
		"emailAddress",
		"message",
		"referrer",
		"dateCreated"
	);
	
	protected $inputSettings = array();
	
	protected $feedbackId,
		$feedbackReasonId,
		$requestFollowUp,
		$emailAddress,
		$message,
		$referrer,
		$dateCreated;
	
	public function getInputs() {
		$this->inputSettings = array(
			"feedbackReasonId" => array(
				"field" => array(
					"type" => "select",
					"name" => "feedbackReasonId",
					"label" => "Reason for contact",
					"required" => "true",
					"choices" => $this->getReasons()
				),
				"rule" => array()
			),
			"requestFollowUp" => array(
				"field" => array(
					"type" => "checkbox",
					"name" => "requestFollowUp",
					"label" => "Request follow-up?",
					"value" => "yes",
					"checked" => "false",
					"attr" => array(
						"data-gd-clear-target" => ".gd-request-follow-up",
						"data-toggle" => "collapse",
						"data-target" => ".gd-request-follow-up"
					)
				),
				"rule" => array()
			),
			"emailAddress" => array(
				"field" => array(
					"type" => "email",
					"name" => "emailAddress",
					"label" => "Email address",
					"autoCapitalize" => "false",
					"maxLength" => "255",
					"submitOnReturn" => "true"
				),
				"rule" => array(
					"stringLength" => array(0, 255),
					"spacesAllowed" => "false",
					"filters" => array("toLowerCase")
				)
			),
			"message" => array(
				"field" => array(
					"type" => "textarea",
					"name" => "message",
					"label" => "Message",
					"placeholder" => "What's on your mind?",
					"maxLength" => "2500",
					"required" => "true",
					"autoCapitalize" => "true"
				),
				"rule" => array(
					"stringLength" => array(1, 1000),
					"spacesAllowed" => "true"
				)
			),
			"referrer" => array(
				"field" => array(
					"type" => "hidden",
					"name" => "referrer"
				),
				"rule" => array(
					"stringLength" => array(0, 255)
				)
			)
		);
		return $this->inputSettings;
	}
	
	public function getReasons() {
		$fr = new FeedbackReason($this->db);
		$fr = $fr->find();
		$reasons = array();
		if ($fr) {
			foreach ($fr as $r) {
				$reasons[] = array( "label" => $r->get("label"), "value" => $r->get("feedbackReasonId"), "selected" => "false");
			}
		}
		return $reasons;
	}
}