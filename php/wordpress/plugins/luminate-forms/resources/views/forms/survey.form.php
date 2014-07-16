<?php
if (isset($_GET["thankYouPageContent"]) && !empty($_GET["thankYouPageContent"])) {
	$html = $_GET["thankYouPageContent"];
	$html = str_replace("\\","", str_replace("/\"", "", $html));
	echo $html;
	return;
}

$response = $loApp->_getSurvey($form_id);

if (empty($response)) {
	echo "<div class=\"alert alert-danger lo-alert lo-alert-danger\"><p><strong>The Luminate Online Survey was not found.</strong></p>";
	echo "<ul><li>Check that the survey ID is correct (the ID supplied in this shortcode was \"{$form_id}\")</li><li>Ensure that the IP address for this website is listed in the IP White List of your Open API settings (IP address: {$_SERVER['SERVER_ADDR']}).</li></ul></div>";
	return;
}

if (isset($response["errorResponse"])) {
	echo $response["errorResponse"]["message"];
	return;
}

$survey = $response["getSurveyResponse"]["survey"];
$questions = $survey["surveyQuestions"];

if (!isset($questions[0])) {
	$questions = array($questions);
}

$loForm = new LuminateForm($loApp, array(
	"name" => "survey",
	"heading" => $survey["surveyName"],
	"action" => $loApp->config("action","survey"),
	"luminateExtendData" => array("callback" => "loForms_surveyCallback"),
	"cssClass" => "lo-form-survey"
));

$submit = new LuminateFormField($loForm, array(
	"name" => "submit",
	"label" => $survey["submitButtonLabel"],
	"type" => "submit",
	"fieldClass" => "btn-primary"
));

$loForm->start();
	$loForm->heading(); 
	
	if ($survey["surveyIntroduction"]) {
		echo "<p class=\"lo-form-introduction\">{$survey['surveyIntroduction']}</p>";
	}
	
	$loForm->alert();
	$loForm->hiddenFields("submitSurvey"); 
	?>
	
	<input type="hidden" name="sso_auth_token" value="<?php echo $loConst->getAuthToken(); ?>">
	<input type="hidden" name="survey_id" value="<?php echo $form_id; ?>">
	<input type="hidden" name="validate" value="true">
	
	<?php 
	foreach ($questions as $question) {
	
		// print_f($question);
	
		$fieldName = "question_{$question['questionId']}";
		$label = $question["questionText"];
		$fieldSettings = array();
		$required = $question["questionRequired"];
		
		switch ($question["questionType"]) {
			
			/* Question Title */
			case "Caption":
			echo "<div class=\"lo-survey-caption\">" . $label . "</div>";
			break;
			
			/* Constituent User Fields */
			case "ConsQuestion":
			echo "<div class=\"lo-survey-caption\">" . $label . "</div>";
			$consFields = $question["questionTypeData"]["consRegInfoData"]["contactInfoField"];
			if (isset($consFields["fieldName"])) { // only one field present; wrap in array for foreach, below
				$consFields = array($consFields);
			}
			foreach ($consFields as $field) {
			
				$value = isset($field["prefillValue"]) ? $field["prefillValue"] : "";
				$required = ($field["fieldStatus"] === "REQUIRED") ? "true" : "false";
				
				// Select list
				if (isset($field["fieldOptionValues"])) {					
					$fieldSettings = array(
						"type" => "select",
						"name" => $field["fieldName"],
						"label" => $field["label"],
						"required" => $required,
						"choices" => $field["fieldOptionValues"],
						"value" => $value
					);
				} 
				
				// Input types...
				else {
				
					switch ($field["fieldName"]) {
					
						case "cons_birth_date":
						$loField = new LuminateFormField($loForm, array(
							"type" => "dateSelect",
							"name" => $field["fieldName"],
							"label" => $field["label"],
							"required" => $required
						));
						$loField->render();
						break;
						
						case "cons_postal_opt_in":
						case "cons_email_opt_in":
						$checked = (isset($field["prefillValue"]) && $field["prefillValue"]=="true") ? "true" : "false";
						$loField = new LuminateFormField($loForm, array(
							"type" => "checkbox",
							"name" => $field["fieldName"],
							"label" => $field["label"],
							"required" => $required,
							"value" => $value,
							"checked" => $checked
						));
						$loField->render();
						break;
						
						default:
						if ($field["label"] === "First" || $field["label"] === "Last") {
							$field["label"] .= " Name";
						}
						$loField = new LuminateFormField($loForm, array(
							"type" => "text",
							"name" => $field["fieldName"],
							"label" => $field["label"],
							"required" => $required,
							"value" => $value,
							"maxLength" => "40"
						));
						$loField->render();
						break;
					}
				}
				
				if (isset($fieldSettings) && count($fieldSettings) > 0) {
					
				}
				
			}
			break;
			
			/* Date Select */
			case "DateQuestion":
			$fieldSettings = array(
				"type" 		=> "dateSelect",
				"name" 		=> $fieldName,
				"label" 	=> $label,
				"required" 	=> $required
			);
			break;
			
			
			/* Hidden Field: Interests */
			case "HiddenInterests":
			$counter=0;
			foreach ($question["questionTypeData"]["surveyQuestionData"]["availableAnswer"] as $field) {
				$counter++;
				echo "<input type=\"hidden\" value=\"".$field["value"]."\" name=\"".$fieldName."_".$counter."\">";
			}
			break;
			
			/* Hidden Fields: Generic */
			case "HiddenTextValue":
			case "HiddenTrueFalse":
			echo "<input type=\"hidden\" value=\"".$label."\" name=\"".$fieldName."\">";
			break;
			
			case "Categories":
			$fieldSettings = array(
				"type" => "interests",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"choices" => $question["questionTypeData"]["surveyQuestionData"]["availableAnswer"],
				"value" => $value
			);
			break;
			
			case "ComboChoice":
			$choiceSelect = new LuminateFormField($loForm, array(
				"type" => "select",
				"name" => $fieldName."_select",
				"label" => "",
				"required" => $required,
				"choices" => $question["questionTypeData"]["surveyQuestionData"]["availableAnswer"],
				"value" => $value,
				"includeWrapper" => "false"
			));
			echo "<div class=\"form-group\">";
				echo "<label>{$label}</label>";
				echo "<div class=\"form-hint\"><small>{$question['questionHint']}</small></div>";
				echo "<div class=\"lo-combo-select-container\">";
					echo "<div class=\"radio\">";
						echo "<input type=\"radio\" name=\"{$fieldName}\" checked=\"checked\" value=\"\">";
						$choiceSelect->render("field");
					echo "</div>";
					echo "<div class=\"radio\">";
						echo "<input type=\"radio\" class=\"lo-combo-select-other\" name=\"{$fieldName}\" value=\"\">";
						echo "<input type=\"text\" disabled=\"disabled\" name=\"".$fieldName."_other\" value=\"\">";
					echo "</div>";
				echo "</div>";
			echo "</div>";
			break;
			
			case "MultiMulti":
			$fieldSettings = array(
				"type" => "checkboxGroup",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"choices" => $question["questionTypeData"]["surveyQuestionData"]["availableAnswer"],
				"value" => $value,
				"hint" => $question["questionHint"]
			);
			break;
			
			case "NumericValue":
			$fieldSettings = array(
				"type" => "text",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"value" => $value,
				"maxLength" => "9"
			);
			break;
			
			case "MultiSingleRadio":
			case "RatingScale":
			$fieldSettings = array(
				"type" => "radioGroup",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"choices" => $question["questionTypeData"]["surveyQuestionData"]["availableAnswer"],
				"value" => $value
			);
			break;
			
			case "ShortTextValue":
			$fieldSettings = array(
				"type" => "text",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"value" => $value,
				"maxLength" => "40"
			);
			break;
			
			case "TextValue":
			$fieldSettings = array(
				"type" => "textarea",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"value" => $value,
				"maxLength" => "255",
				"rows" => "5",
				"hint" => $question["questionHint"]
			);
			break;
			
			case "LargeTextValue":
			$fieldSettings = array(
				"type" => "textarea",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"value" => $value,
				"maxLength" => "255000",
				"rows" => "10"
			);
			break;
			
			case "MultiSingle":
			case "TrueFalse":
			case "YesNo":
			$fieldSettings = array(
				"type" => "select",
				"name" => $fieldName,
				"label" => $label,
				"required" => $required,
				"choices" => $question["questionTypeData"]["surveyQuestionData"]["availableAnswer"],
				"value" => $value
			);
			break;
			
			case "Captcha":
				/*
				echo "<div class=\"captchaContainer\">";
					echo "<label for=\"captcha_input\">".$label."</label>";
					echo "<div><img id=\"lo_captcha_img\" src=\"".$question["questionTypeData"]["captchaData"]["imageSource"]."\"></div>";
					echo "<div><a href=\"#\" onclick=\"lo_captcha_change_img()\">".$question["questionTypeData"]["captchaData"]["changeImageLabel"]."</a><br><a href=\"".$question["questionTypeData"]["captchaData"]["audioLink"]."\" title=\"".$question["questionTypeData"]["captchaData"]["audioLinkLabel"]."\">Visually Impaired?</a></div>";
					echo "<div><input type=\"text\" id=\"captcha_input\" name=\"".$fieldName."\"></div>";
					?>
					<script>
					function lo_captcha_change_img() {
						var ts = new Date().getTime();
						document.getElementById('lo_captcha_img').src='<?php echo $question["questionTypeData"]["captchaData"]["imageSource"]; ?>?ts='+ts;
						document.getElementById('lo_captcha_img').style.display='';
						document.getElementById('captcha_player_583_2261_19_2096').style.display='none';
					}
					//
					// Important note: The T7 forcible rewriting of the URL is necessary because
					// the .wav file is going to be played by Windows Media Player
					// not by the browser. It will not connect to the same session
					// as the form unless the URL is forcibly rewritten. This results
					// in it being absolutely impossible to get the CAPTCHA question
					// correct, adding insult to injury for the visually impaired user.
					//
					function audio_challenge_583_2261_19_2096 () {
						var ts = new Date().getTime();
						document.getElementById('lo_captcha_img').style.display='none';
						document.getElementById('captcha_player_583_2261_19_2096').style.display='';
						
						document.getElementById('captcha_player_583_2261_19_2096').innerHTML = '<OBJECT '
						+ 'CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" '
						+ 'WIDTH="160" HEIGHT="50" '
						+ 'CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab">'
						+ '<param name="SRC" value="<?php echo $question["questionTypeData"]["captchaData"]["imageSource"]; ?>?type=audio&JServSessionIda005=7oxr6mcmp1.adm8009' + '&ts=' + ts + '" /> ' 
						+ '<PARAM name="AUTOPLAY" VALUE="true">'
						+ '<PARAM name="CONTROLLER" VALUE="false">'
						+ '<PARAM name="VOLUME" VALUE="100">'
						+ '<PARAM name="ENABLEJAVASCRIPT" VALUE="true">'
						+ '<PARAM name="TYPE" VALUE="audio/wav">'
						+ '<embed classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"' 
						+ 'name="sound"'
						+ 'id="sound"' 
						+ 'src="<?php echo $question["questionTypeData"]["captchaData"]["imageSource"]; ?>?type=audio&JServSessionIda005=7oxr6mcmp1.adm8009' + '&ts=' + ts + '"' 
						+ 'pluginspage="http://www.apple.com/quicktime/download/"'
						+ 'volume="100"' 
						+ 'enablejavascript="true" '
						+ 'type="audio/wav" '
						+ 'height="50" '
						+ 'width="160"'
						+ 'autostart="true"'
						+ '> </embed>'
						+ '</OBJECT>';
					}
					</script>
					<?php
				echo "</div>";
				*/
			break;
		}
		
		if (count($fieldSettings) > 0) {
			$loField = new LuminateFormField($loForm, $fieldSettings);
			$loField->render();
		}
		unset($fieldSettings);
		
	}
	$submit->render();
$loForm->stop();
