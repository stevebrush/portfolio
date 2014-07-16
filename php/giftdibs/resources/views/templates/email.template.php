<html>
	<body>
		<style type="text/css">
			@media only screen and (max-device-width: 480px) {
				table[class=email-table] {
					width:100% !important;
				}
				table[class=email-footer] td {
					font-size:.9em !important;
					width:auto !important;
					padding:0!important;
				}
				table[class=email-footer] img {
					display:none;
				}
				td, p {font-size:1.1em !important;line-height:1.2em !important;}
			}
		</style>
			
		<table style="text-align:left;width:100%;border:0;background:#eee;font-family:sans-serif;font-size:14px;line-height:1.3;">
			<tr>
				<td style="padding:20px;text-align:center;vertical-align:top;">
					<table class="email-table" style="text-align:left;font-family:sans-serif;font-size:14px;line-height:1.3;background:#fff;width:550px;border:0;margin:0 auto;border:1px solid #ddd;">
						<tr>
							<td class="email-header" style="font-family:sans-serif;font-size:14px;line-height:1.3;padding:15px 15px 10px;">
								<h1 style="margin:0;padding:0;font-size:18px;"><?php echo $emailVars["title"]; ?></h1>
							</td>
						</tr>
						<tr>
							<td class="email-body" style="padding:15px;font-family:sans-serif;font-size:14px;line-height:1.3;">
								<?php echo $emailVars["body"]; ?>
							</td>
						</tr>
						<tr>
							<td class="email-footer" style="text-align:center;padding:15px;background:#eee;font-family:sans-serif;font-size:14px;line-height:1.3;">
								<p><a href="<?php echo $emailVars["emailPreferencesUrl"]; ?>">Email preferences</a> | <a href="<?php echo $emailVars["privacyUrl"]; ?>">Privacy</a></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>