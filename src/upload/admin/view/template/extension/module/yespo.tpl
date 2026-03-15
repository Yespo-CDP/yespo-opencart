<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		
		<div class="panel panel-default" style="border:none; box-shadow:none; background:transparent;">
			<div class="panel-heading top-panel-heading" id="original-logo-header">
				<svg width="90" height="40" viewBox="0 0 90 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M45.7399 15.7L43.5383 21.2059L41.3015 15.7H37.9375L41.9708 24.2765L39.6988 29.5H42.7986L48.8749 15.7H45.7399Z" fill="#0B266B"/>
                <path d="M54.8802 22.7236C54.4927 22.9177 54.0348 23.0236 53.5064 23.0236C53.0308 23.0236 52.6258 22.9177 52.3087 22.7236C51.9917 22.5118 51.7451 22.1942 51.5866 21.7883C51.4986 21.5589 51.4281 21.2942 51.3929 21.0118H58.5083C58.5436 20.9236 58.5436 20.8 58.5436 20.6589C58.5436 20.5177 58.5436 20.3589 58.5436 20.2177C58.5436 19.2118 58.3498 18.3648 57.9624 17.6589C57.5749 16.953 56.9937 16.4236 56.2539 16.053C55.5142 15.6824 54.5984 15.4883 53.5064 15.4883C52.432 15.4883 51.4809 15.6824 50.6884 16.0706C49.8958 16.4589 49.297 17.0236 48.8743 17.7471C48.4516 18.4706 48.2402 19.3353 48.2402 20.3412C48.2402 21.3295 48.4516 22.1942 48.8919 22.9177C49.3322 23.6412 49.931 24.2236 50.7236 24.6295C51.5162 25.0177 52.4672 25.2118 53.5416 25.2118C54.7393 25.2118 55.7432 24.9824 56.571 24.5236C57.4164 24.0648 58.068 23.3765 58.5612 22.4589L55.8136 21.7883C55.5847 22.2118 55.2676 22.5118 54.8802 22.7236ZM52.2911 17.853C52.6081 17.6589 52.9956 17.553 53.4535 17.553C53.8234 17.553 54.1404 17.6412 54.4046 17.8C54.6688 17.9589 54.8802 18.1883 55.0387 18.4883C55.162 18.7353 55.2324 19 55.25 19.3177H51.4281C51.4633 19.1236 51.5162 18.9471 51.569 18.7883C51.7275 18.3648 51.9741 18.0471 52.2911 17.853Z" fill="#0B266B"/>
                <path d="M65.8007 20.1294C65.3956 19.9 64.9553 19.6883 64.4622 19.5294C64.0043 19.353 63.6168 19.2118 63.2821 19.1059C62.9651 18.9824 62.7362 18.8589 62.56 18.7177C62.4015 18.5765 62.3134 18.4177 62.3134 18.2059C62.3134 18.0294 62.3839 17.8883 62.5248 17.7824C62.6657 17.6765 62.8947 17.6236 63.1765 17.6236C63.5992 17.6236 64.0571 17.7471 64.5502 17.9941C65.0434 18.2236 65.5013 18.5236 65.8888 18.8941L66.9808 16.9C66.6109 16.6353 66.2234 16.3883 65.8007 16.1941C65.3956 16 64.9553 15.8236 64.4798 15.7C64.0219 15.5765 63.5111 15.5236 62.9827 15.5236C62.3135 15.5236 61.697 15.6294 61.1686 15.8236C60.6579 16.0177 60.2352 16.3177 59.9358 16.7236C59.6363 17.1118 59.4954 17.5883 59.4954 18.1706C59.4954 18.7706 59.6187 19.2471 59.8829 19.6353C60.1471 20.0059 60.4641 20.3236 60.8516 20.553C61.2567 20.7647 61.6794 20.9589 62.1373 21.1177C62.6305 21.2589 63.0356 21.4 63.3174 21.5412C63.6168 21.6824 63.8281 21.8236 63.9514 21.9647C64.0747 22.1059 64.1275 22.2647 64.1275 22.4765C64.1275 22.6883 64.0571 22.8647 63.8986 23.0059C63.7577 23.1471 63.5287 23.2 63.2117 23.2C63.0003 23.2 62.7362 23.1647 62.4367 23.0765C62.1373 22.9883 61.8027 22.8294 61.3976 22.6177C61.0277 22.3883 60.5874 22.0706 60.1119 21.6824L58.8262 23.7118C59.2489 24.0294 59.7068 24.3294 60.1823 24.6118C60.6755 24.8765 61.2039 25.0706 61.7675 25.2118C62.3487 25.3706 62.9827 25.4589 63.652 25.4589C64.7616 25.4589 65.6246 25.2118 66.2234 24.7C66.8399 24.1883 67.1569 23.4294 67.1569 22.4236C67.1569 21.8412 67.0336 21.3824 66.7694 21.0294C66.5581 20.6412 66.2234 20.3589 65.8007 20.1294Z" fill="#0B266B"/>
                <path d="M76.4383 15.9471C75.769 15.5765 75.0645 15.3824 74.2896 15.3824C73.4794 15.3824 72.7749 15.5941 72.1761 16.0177C71.7357 16.3177 71.3835 16.7236 71.1017 17.2V15.7H68.0371V29.5H71.1017V23.5353C71.3835 24.0294 71.7357 24.4353 72.1761 24.7353C72.7749 25.1412 73.4794 25.3353 74.2896 25.3353C75.0645 25.3353 75.7866 25.1412 76.4383 24.7706C77.1076 24.4 77.6712 23.8353 78.0762 23.0941C78.5166 22.353 78.7279 21.4353 78.7279 20.3589C78.7279 19.2647 78.5166 18.3471 78.0762 17.6236C77.6712 16.8824 77.1076 16.3177 76.4383 15.9471ZM75.2054 21.6118C74.9941 21.9471 74.7299 22.2118 74.3952 22.3883C74.0606 22.5647 73.6907 22.653 73.2856 22.653C72.9334 22.653 72.5811 22.5647 72.2465 22.4059C71.9295 22.2294 71.6477 21.9647 71.4363 21.6294C71.225 21.2942 71.1193 20.8706 71.1193 20.3765C71.1193 19.8824 71.225 19.4589 71.4363 19.1236C71.6477 18.7883 71.9295 18.5412 72.2465 18.3647C72.5811 18.1883 72.9334 18.1 73.2856 18.1C73.6907 18.1 74.0606 18.1883 74.3952 18.3647C74.7299 18.5412 75.0117 18.8059 75.2054 19.1412C75.3991 19.4765 75.5048 19.9 75.5048 20.3765C75.5048 20.853 75.3991 21.2589 75.2054 21.6118Z" fill="#0B266B"/>
                <path d="M89.1894 17.8C88.7314 17.0765 88.0974 16.4942 87.2872 16.0883C86.4946 15.6824 85.5788 15.4883 84.5749 15.4883C83.571 15.4883 82.6551 15.6824 81.8273 16.0883C81.0171 16.4942 80.3831 17.0589 79.9252 17.8C79.4672 18.5236 79.2383 19.3883 79.2383 20.3765C79.2383 21.3471 79.4672 22.1942 79.9252 22.9353C80.3831 23.6589 81.0171 24.2412 81.8273 24.6471C82.6375 25.053 83.5534 25.2471 84.5749 25.2471C85.5964 25.2471 86.4946 25.053 87.2872 24.6471C88.0974 24.2412 88.7314 23.6765 89.1894 22.9353C89.6649 22.2118 89.8939 21.3471 89.8939 20.3765C89.8939 19.3883 89.6649 18.5236 89.1894 17.8ZM86.4242 21.6295C86.2305 21.9648 85.9663 22.2295 85.6316 22.4236C85.3146 22.6177 84.9447 22.7059 84.5573 22.7059C84.1522 22.7059 83.7823 22.6177 83.4653 22.4236C83.1483 22.2295 82.8841 21.9648 82.6727 21.6295C82.4614 21.2765 82.3733 20.853 82.3733 20.3765C82.3733 19.9 82.479 19.4765 82.6727 19.1236C82.8841 18.7706 83.1483 18.5059 83.4653 18.3118C83.7823 18.1177 84.1522 18.0295 84.5573 18.0295C84.9447 18.0295 85.3146 18.1177 85.6316 18.3118C85.9663 18.5059 86.2305 18.7706 86.4242 19.1236C86.6355 19.4765 86.7236 19.9 86.7236 20.3765C86.7236 20.853 86.6355 21.2765 86.4242 21.6295Z" fill="#0B266B"/>
                <path d="M29.9413 22C29.9413 18.0294 28.3562 14.2 25.5558 11.3941C22.7554 8.58824 18.9335 7 14.9706 7C11.0078 7 7.18591 8.58824 4.38552 11.3941C1.58513 14.2 0 18.0294 0 22H14.9706H29.9413Z" fill="#EBFF00"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M29.9413 20.8882H19.1272L28.4971 15.4706L27.3875 13.5294L18.0176 18.9471L23.4247 9.57647L21.4873 8.44706L16.0802 17.8353V7H13.8611V17.8353L8.45401 8.44706L6.51663 9.57647L11.9237 18.9471L2.57143 13.5294L1.44423 15.4706L10.8141 20.8882H0V23.1118H13.0333L6.51663 34.4235L8.45401 35.5529L15.3757 23.5176V23.5353L15.6223 23.1118H29.9413V20.8882Z" fill="#0B266B"/>
				</svg>
			</div>
			<div class="panel-body" id="sync-form-container" <?php if ($yespo_api_key) { ?>style="display:none;"<?php } ?>>
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-yespo" class="form-horizontal">
					<div class="sync-card custom-sync-form" style="background:#fff; border:1px solid #ddd;">
						<div class="api-form">
							<h2><?php echo $text_data_sync; ?></h2>
							<p class="text-heading"><?php echo $text_in_head; ?></p>
							<div class="form-group" style="margin:0;">
								<label class="control-label" for="input-api-key"><?php echo $entry_api_key; ?></label>
								<input type="text" name="temp_api_key" id="input-api-key" class="form-control custom-input" value="<?php echo $yespo_api_key; ?>" />
								<div class="help-text">
									<?php echo $text_key_help; ?>
								</div>
								<div id="initial-error-log"></div>
							</div>
							<div class="form-group" style="margin:0;">
								<button type="button" id="button-sync" class="btn btn-primary btn-lg custom-btn">
									<?php echo $button_sync; ?>
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="yespo-steps-container" id="integration-steps" <?php if (!$yespo_api_key) { ?>style="display:none;"<?php } ?>>
				
				<div class="step-card" id="step-account">
					<div class="step-icon">
						<div class="status-default" style="display:none;"></div>
						<img src="view/image/yespo/yespo-loading.png" class="status-loading status-icon yespo-spin" style="display:none;" alt="loading">
						<img src="view/image/yespo/yespo-success.png" class="status-success status-icon" style="display:none;" alt="success">
						<img src="view/image/yespo/yespo-error.png" class="status-error status-icon" style="display:none;" alt="error">
					</div>
					<div class="step-content">
						<h4 class="step-title"><?php echo $text_account_connection; ?></h4>
						<p class="text-muted status-success-msg" style="margin:0; display:none;"><?php echo $text_api_key_connected; ?> <strong><span id="org-name"><?php echo $yespo_orgname; ?></span></strong></p>
						
						<div class="status-error-msg" style="display:none;">
							<p class="text-muted" style="color: #666; margin: 0;"><?php echo $error_api_key; ?></p>
						</div>
					</div>
					<div class="step-action">
						<a href="<?php echo $disconnect; ?>" class="btn btn-default btn-disconnect" style="display:none;" onclick="return confirm('<?php echo $text_you_sure; ?>');"><?php echo $text_disconnect; ?></a>
						<a href="<?php echo $disconnect; ?>" class="btn btn-primary btn-try-again" style="display:none;"><?php echo $text_try_again; ?></a>
					</div>
				</div>

				<div class="step-card" id="step-tracking" style="display:none;">
					<div class="step-icon">
						<div class="status-default"></div>
						<img src="view/image/yespo/yespo-loading.png" class="status-loading status-icon yespo-spin" style="display:none;" alt="loading">
						<img src="view/image/yespo/yespo-success.png" class="status-success status-icon" style="display:none;" alt="success">
						<img src="view/image/yespo/yespo-error.png" class="status-error status-icon" style="display:none;" alt="error">
					</div>
					<div class="step-content">
						<h4 class="step-title"><?php echo $text_web_tracking; ?></h4>
						<p class="text-muted success-msg" style="display:none; margin:0;"><?php echo $text_site_script_installed; ?></p>
						<p class="text-muted error-msg" style="display:none; margin:0;"><?php echo $error_site_script; ?></p>
					</div>
					<div class="step-action">
						 <div class="error-actions" style="display:none; display:flex; gap:10px;">
							<a href="https://yespo.io/contact-us" target="_blank" class="btn btn-default btn-support"><?php echo $button_support; ?></a>
							<button type="button" class="btn btn-primary btn-retry-step btn-tryagain"><?php echo $text_try_again; ?></button>
						 </div>
					</div>
				</div>

				<div class="step-card" id="step-push" style="display:none;">
					<div class="step-icon">
						<div class="status-default"></div>
						<img src="view/image/yespo/yespo-loading.png" class="status-loading status-icon yespo-spin" style="display:none;" alt="loading">
						<img src="view/image/yespo/yespo-success.png" class="status-success status-icon" style="display:none;" alt="success">
						<img src="view/image/yespo/yespo-error.png" class="status-error status-icon" style="display:none;" alt="error">
					</div>
					<div class="step-content">
						<h4 class="step-title"><?php echo $text_web_push; ?></h4>
						<p class="text-muted success-msg" style="display:none; margin:0;"><?php echo $text_web_push_success; ?></p>
						<p class="text-muted error-msg" style="display:none; margin:0;"><?php echo $error_web_push; ?></p>
					</div>
					<div class="step-action">
						 <div class="error-actions" style="display:none; display:flex; gap:10px;">
							<a href="https://yespo.io/contact-us" target="_blank" class="btn btn-default btn-support"><?php echo $button_support; ?></a>
							<button type="button" class="btn btn-primary btn-retry-step btn-tryagain"><?php echo $text_try_again; ?></button>
						 </div>
					</div>
				</div>

				<div class="step-card" id="step-import" style="display:none;">
					<div class="step-icon">
						<div class="status-default"></div>
						<img src="view/image/yespo/yespo-loading.png" class="status-loading status-icon yespo-spin" style="display:none;" alt="loading">
						<img src="view/image/yespo/yespo-success.png" class="status-success status-icon" style="display:none;" alt="success">
						<img src="view/image/yespo/yespo-error.png" class="status-error status-icon" style="display:none;" alt="error">
					</div>
					<div class="step-content" style="flex-grow: 1; padding-right: 15px;">
						<h4 class="step-title"><?php echo $text_import_history; ?></h4>
						
						<div class="import-wrapper">
							<p class="text-muted import-desc status-loading-msg" style="display:none; margin:0;"><?php echo $text_first_export; ?></p>
							<p class="text-muted import-desc status-success-msg" style="display:none; margin:0;"><?php echo $text_import_success; ?></p>
							<p class="text-danger import-error-msg" style="display:none; margin-top:5px; line-height: 1.4;"></p>
							
							<div class="import-stats-table">
								<div class="stat-row header">
									<div class="col-name"></div>
									<div class="col-val"><?php echo $text_loading_total; ?></div>
									<div class="col-val"><?php echo $text_loading_synchronized; ?></div>
									<div class="col-val"><?php echo $text_loading_success; ?></div>
									<div class="col-val"><?php echo $text_loading_failed; ?></div>
								</div>
								<div class="stat-row">
									<div class="col-name" style="display: flex; align-items: center; gap: 10px;">
										<div id="icon-customers-status" style="width:16px; height:16px;">
											<div class="status-default-sm"></div>
											<img src="view/image/yespo/yespo-loading.png" class="status-loading status-icon-sm yespo-spin" style="display:none;" alt="loading">
											<img src="view/image/yespo/yespo-success.png" class="status-success status-icon-sm" style="display:none;" alt="success">
											<img src="view/image/yespo/yespo-error.png" class="status-error status-icon-sm" style="display:none;" alt="error">
										</div>
										<?php echo $text_contacts; ?>
									</div>
									<div class="col-val" id="val-customers-total">-</div>
									<div class="col-val" id="val-customers-synced">-</div>
									<div class="col-val text-success" id="val-customers-success">-</div>
									<div class="col-val text-danger" id="val-customers-failed">-</div>
								</div>
								<div class="stat-row">
									<div class="col-name" style="display: flex; align-items: center; gap: 10px;">
										<div id="icon-orders-status" style="width:16px; height:16px;">
											<div class="status-default-sm"></div>
											<img src="view/image/yespo/yespo-loading.png" class="status-loading status-icon-sm yespo-spin" style="display:none;" alt="loading">
											<img src="view/image/yespo/yespo-success.png" class="status-success status-icon-sm" style="display:none;" alt="success">
											<img src="view/image/yespo/yespo-error.png" class="status-error status-icon-sm" style="display:none;" alt="error">
										</div>
										 <?php echo $text_orders; ?>
									</div>
									<div class="col-val" id="val-orders-total">-</div>
									<div class="col-val" id="val-orders-synced">-</div>
									<div class="col-val text-success" id="val-orders-success">-</div>
									<div class="col-val text-danger" id="val-orders-failed">-</div>
								</div>
							</div>
						</div>
					</div>
					<div class="step-action">
						 <div class="error-actions" style="display:none; display:flex; gap:10px;">
							<button type="button" class="btn btn-primary btn-retry-import btn-tryagain"><?php echo $text_try_again; ?></button>
						 </div>
					</div>
				</div>

			</div>
			
			<div class="made text-right" style="margin-top: 20px;"><?php echo $text_made; ?></div>
		</div>
	</div>
</div>

<style>
.custom-sync-form { padding: 20px; font-family: Arial, sans-serif; }
.custom-input { border: none; border-bottom: 2px solid #1e91cf; box-shadow: none; border-radius: 0; padding-left: 0; padding-right: 0; height: 40px; background: transparent; width: 100%; font-size: 17px; max-width: 600px; }
.custom-input:hover, .custom-input:focus { border-bottom: 2px solid #1e91cf; box-shadow: none; outline: none; }
.custom-sync-form .form-control:hover, .custom-sync-form .form-control:focus { border-top: 0; border-left: 0; border-right: 0; box-shadow: none; }
.help-text { margin-top: 10px; color: #999; font-size: 15px; padding-top: 15px; max-width: 600px; }
.custom-btn { margin-top: 20px; background-color: #1a64a7; border-color: #1a64a7; border-radius: 4px; padding: 10px 30px; font-size: 16px; color: #fff; }
.custom-btn:hover { background-color: #145086; color: #fff; }

.status-icon { width: 24px; height: 24px; display: inline-block; vertical-align: middle; }
.status-error.status-icon { width: 36px; height: 36px; margin-top: -6px; }
.status-default { width: 24px; height: 24px; border-radius: 50%; border: 2px solid #ccc; display: inline-block; box-sizing: border-box; }
.status-icon-sm { width: 16px; height: 16px; display: inline-block; vertical-align: middle; }
.status-default-sm { width: 16px; height: 16px; border-radius: 50%; border: 2px solid #ccc; display: inline-block; box-sizing: border-box; }

.yespo-spin { animation: yespo-spin-anim 1.5s linear infinite; }
@keyframes yespo-spin-anim { 100% { transform: rotate(360deg); } }

.yespo-steps-container { background: #fff; border: 1px solid #ddd; border-radius: 3px; padding: 0; }
.step-card { display: flex; align-items: flex-start; padding: 20px; border-bottom: 1px solid #e1e1e1; }
.step-card:last-child { border-bottom: none; }
.step-icon { width: 40px; margin-right: 15px; text-align: center; padding-top: 2px; }
.step-content { flex-grow: 1; }
.step-action { min-width: 150px; text-align: right; display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px; }
.step-title { margin: 0 0 5px 0; font-size: 16px; font-weight: 500; color: #333; }
.text-muted { color: #777; font-size: 14px; }

.btn.btn-disconnect, .btn.btn-support, .btn.btn-tryagain, .btn.btn-try-again { padding: 8px 15px; border-radius: 4px; font-size: 16px; box-shadow: 0px 0px 2px 0px rgba(0, 0, 0, 0.25), 0px 2px 4px 0px rgba(0, 0, 0, 0.12); }
.btn.btn-tryagain, .btn.btn-try-again { background: #2b64cd; color: #fff; border-color: #2b64cd; }
.btn.btn-tryagain:hover, .btn.btn-try-again:hover { background: #1a4b9e; color: #fff; border-color: #1a4b9e; }

.import-stats-table { margin-top: 15px; width: 100%; max-width: 800px; }
.stat-row { display: flex; padding: 10px 0; border-bottom: 1px solid #e1e1e1; align-items: center; }
.stat-row.header { color: #999; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #eee; }
.stat-row .col-name { width: 150px; font-weight: 500; }
.stat-row .col-val { width: 120px; text-align: left; }
</style>

<script type="text/javascript">
var hasApiKey = <?php echo $yespo_api_key ? 'true' : 'false'; ?>;
var isModuleActive = <?php echo $yespo_status == '1' ? 'true' : 'false'; ?>;
var hasSiteScript = <?php echo $yespo_site_script ? 'true' : 'false'; ?>;
var hasWebPush = <?php echo $yespo_web_push ? 'true' : 'false'; ?>;

var custLoaded = ('<?php echo $yespo_customers_loaded; ?>' === '1');
var ordersLoaded = ('<?php echo $yespo_orders_loaded; ?>' === '1');

var rawCustPage = '<?php echo $yespo_customers_page; ?>'.replace(/\D/g, '');
var custPage = parseInt(rawCustPage, 10) || 1;

var rawCustLimit = '<?php echo $customers_limit; ?>'.replace(/\D/g, '');
var custLimit = parseInt(rawCustLimit, 10) || 2000;

var rawOrdersPage = '<?php echo $yespo_orders_page; ?>'.replace(/\D/g, '');
var ordersPage = parseInt(rawOrdersPage, 10) || 1;

var rawOrdersLimit = '<?php echo $orders_limit; ?>'.replace(/\D/g, '');
var ordersLimit = parseInt(rawOrdersLimit, 10) || 300;

var customersStats = { 
	total: parseInt('<?php echo $total_customers_db; ?>'.replace(/\D/g, ''), 10) || 0, 
	synced: 0, 
	success: 0, 
	failed: parseInt('<?php echo $yespo_bad_customers; ?>'.replace(/\D/g, ''), 10) || 0 
};
var ordersStats = { 
	total: parseInt('<?php echo $total_orders_db; ?>'.replace(/\D/g, ''), 10) || 0, 
	synced: 0, 
	success: 0, 
	failed: parseInt('<?php echo $yespo_bad_orders; ?>'.replace(/\D/g, ''), 10) || 0 
};

var isCustomersDone = false;
var isOrdersDone = false;

var maxRetries = 5;
var custRetries = 0;
var ordersRetries = 0;

var custFailed = false;
var ordersFailed = false;

$(document).ready(function() {
	if (hasApiKey) {
		verifyApiKeyOnLoad();
	}
});

function verifyApiKeyOnLoad() {
	var apiKey = $('#input-api-key').val();
	setStepStatus('step-account', 'loading');
	
	$.ajax({
		url: '<?php echo $check_api_key; ?>',
		type: 'post',
		data: { api_key: apiKey },
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				setStepStatus('step-account', 'success');
				
				if (custLoaded) {
					isCustomersDone = true;
					customersStats.synced = customersStats.total;
					customersStats.success = Math.max(0, customersStats.total - customersStats.failed);
				} else {
					customersStats.synced = (custPage > 1) ? (custPage - 1) * custLimit : 0;
					customersStats.success = Math.max(0, customersStats.synced - customersStats.failed);
				}

				if (ordersLoaded) {
					isOrdersDone = true;
					ordersStats.synced = ordersStats.total;
					ordersStats.success = Math.max(0, ordersStats.total - ordersStats.failed);
				} else {
					ordersStats.synced = (ordersPage > 1) ? (ordersPage - 1) * ordersLimit : 0;
					ordersStats.success = Math.max(0, ordersStats.synced - ordersStats.failed);
				}

				startProcesses();
			} else {
				setStepStatus('step-account', 'error');
			}
		},
		error: function() {
			setStepStatus('step-account', 'error');
		}
	});
}

function showInitialError(msg) {
	var errHtml = '<div style="display:flex; align-items:flex-start; margin-top:20px;">' + 
				  '<div style="width:40px; margin-right:15px; text-align:center; padding-top:2px;">' +
				  '<img src="view/image/yespo/yespo-error.png" class="status-icon" alt="error">' +
				  '</div>' +
				  '<div style="flex-grow:1; display:flex; align-items:center; min-height: 24px;"><p class="text-muted" style="color:#666; margin:0;">' + msg + '</p></div>' +
				  '</div>';
	$('#initial-error-log').html(errHtml);
}

$('#button-sync').on('click', function() {
	var apiKey = $('#input-api-key').val();
	var $btn = $(this);
	
	$('#initial-error-log').empty();

	if (!apiKey) {
		showInitialError('<?php echo $error_api_key_required; ?>');
		return;
	}
	
	$btn.button('loading');
	
	$.ajax({
		url: '<?php echo $check_api_key; ?>',
		type: 'post',
		data: { api_key: apiKey },
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				$('#sync-form-container').slideUp();
				$('#org-name').text(json['org_name']);
				$('#integration-steps').slideDown();
				
				if (json['reset']) {
					hasApiKey = true;
					isModuleActive = false;
					custPage = 1;
					ordersPage = 1;
					customersStats.synced = 0;
					ordersStats.synced = 0;
					customersStats.failed = 0;
					ordersStats.failed = 0;
					customersStats.success = 0;
					ordersStats.success = 0;
					custLoaded = false;
					ordersLoaded = false;
					hasSiteScript = false;
					hasWebPush = false;
					isCustomersDone = false;
					isOrdersDone = false;
				}
				
				setStepStatus('step-account', 'success');
				startProcesses(); 
			} else {
				$btn.button('reset');
				showInitialError('<?php echo $error_api_key; ?>');
			}
		},
		error: function() {
			$btn.button('reset');
			showInitialError('<?php echo $error_connection; ?>');
		}
	});
});

function startProcesses() {
	$('#step-tracking').slideDown(0);
	$('#step-push').slideDown(0);
	$('#step-import').slideDown(0);

	if (!hasSiteScript) {
		getSiteScript(1);
	} else {
		setStepStatus('step-tracking', 'success');
	}

	if (!hasWebPush) {
		addWebPush(1);
	} else {
		setStepStatus('step-push', 'success');
	}

	startImports();
}

function setStepStatus(stepId, status) {
	var $step = $('#' + stepId);
	$step.find('.step-icon').children().hide();
	
	if (status == 'default') {
		$step.find('.status-default').show();
	} else if (status == 'loading') {
		$step.find('.status-loading').show();
		
		if (stepId == 'step-tracking' || stepId == 'step-push') {
			$step.find('.error-msg').hide();
			$step.find('.error-actions').hide();
		}
		if (stepId == 'step-account') {
			$step.find('.status-error-msg').hide();
			$step.find('.btn-try-again').hide();
		}
		if (stepId == 'step-import') {
			$step.find('.error-actions').hide();
		}
		
	} else if (status == 'success') {
		$step.find('.status-success').show();
		
		if (stepId == 'step-account') {
			$step.find('.status-success-msg').show();
			$step.find('.status-error-msg').hide();
			$step.find('.btn-disconnect').show();
			$step.find('.btn-try-again').hide();
		}
		if (stepId == 'step-import') {
			$('.status-loading-msg').hide();
			$('.import-error-msg').hide();
			$('.status-success-msg').show();
			$step.find('.error-actions').hide();
		}
		if (stepId == 'step-tracking' || stepId == 'step-push') {
			$step.find('.success-msg').show();
			$step.find('.error-msg').hide();
			$step.find('.error-actions').hide();
		}
	} else if (status == 'error') {
		$step.find('.status-error').show();
		
		if (stepId == 'step-account') {
			$step.find('.status-success-msg').hide();
			$step.find('.status-error-msg').show();
			$step.find('.btn-disconnect').hide();
			$step.find('.btn-try-again').show();
		}
		if (stepId == 'step-tracking' || stepId == 'step-push') {
			$step.find('.success-msg').hide();
			$step.find('.error-msg').show();
			$step.find('.error-actions').css('display', 'flex'); 
		}
		if (stepId == 'step-import') {
			$step.find('.status-loading-msg').hide();
			$step.find('.status-success-msg').hide();
			$step.find('.error-actions').css('display', 'flex'); 
		}
	}
}

function updateImportUI(type, status) {
	var $container = $('#icon-' + type + '-status');
	$container.children().hide();
	
	if (status == 'loading') {
		$container.find('.status-loading').show();
	} else if (status == 'success') {
		$container.find('.status-success').show();
	} else if (status == 'error') {
		$container.find('.status-error').show();
	} else {
		$container.find('.status-default-sm').show();
	}
}

function updateImportStats(type) {
	var stats = (type == 'customers') ? customersStats : ordersStats;
	
	if (stats.total > 0 && stats.synced > stats.total) {
		stats.synced = stats.total;
	}
	if (stats.success > stats.total) {
		stats.success = stats.total;
	}
	
	$('#val-' + type + '-total').text(stats.total > 0 ? stats.total : '-');
	$('#val-' + type + '-synced').text(stats.synced);
	$('#val-' + type + '-success').text(stats.success);
	$('#val-' + type + '-failed').text(stats.failed);
}

function checkImportCompletion() {
	if (isCustomersDone && isOrdersDone) {
		setStepStatus('step-import', 'success');
		
		if (!isModuleActive) {
			$.ajax({
				url: '<?php echo $set_active; ?>',
				type: 'post',
				dataType: 'json',
				success: function(json) {
					if(json['success']) {
						isModuleActive = true;
					}
				}
			});
		}
	}
}

function handleImportError(type, msg) {
	if (type === 'customers') custFailed = true;
	if (type === 'orders') ordersFailed = true;
	
	updateImportUI(type, 'error');
	setStepStatus('step-import', 'error');
	
	var $errorBox = $('.import-error-msg');
	var prefix = (type === 'customers') ? '<?php echo $text_failed; ?> <?php echo $text_contacts; ?>' : '<?php echo $text_failed; ?> <?php echo $text_orders; ?>';
	var currentHtml = $errorBox.html();
	
	if (currentHtml && currentHtml.indexOf(prefix) === -1) {
		$errorBox.html(currentHtml + '<br><b>' + prefix + ':</b> ' + msg);
	} else if (!currentHtml) {
		$errorBox.html('<b>' + prefix + ':</b> ' + msg);
	}
	
	$errorBox.show();
}

function getSiteScript(retryCount) {
	setStepStatus('step-tracking', 'loading');
	$.ajax({
		url: '<?php echo $get_site_script; ?>',
		type: 'post',
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				hasSiteScript = true;
				setStepStatus('step-tracking', 'success');
			} else {
				if (retryCount < maxRetries) {
					setTimeout(function() { getSiteScript(retryCount + 1); }, 3000);
				} else {
					setStepStatus('step-tracking', 'error');
				}
			}
		},
		error: function() {
			if (retryCount < maxRetries) {
				setTimeout(function() { getSiteScript(retryCount + 1); }, 3000);
			} else {
				setStepStatus('step-tracking', 'error');
			}
		}
	});
}

function addWebPush(retryCount) {
	setStepStatus('step-push', 'loading');
	$.ajax({
		url: '<?php echo $add_web_push; ?>',
		type: 'post',
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				hasWebPush = true;
				setStepStatus('step-push', 'success');
			} else {
				if (retryCount < maxRetries) {
					setTimeout(function() { addWebPush(retryCount + 1); }, 3000);
				} else {
					setStepStatus('step-push', 'error');
				}
			}
		},
		error: function() {
			if (retryCount < maxRetries) {
				setTimeout(function() { addWebPush(retryCount + 1); }, 3000);
			} else {
				setStepStatus('step-push', 'error');
			}
		}
	});
}

function startImports() {
	setStepStatus('step-import', 'loading');
	$('.import-desc.status-loading-msg').show();

	if (!custLoaded) {
		updateImportUI('customers', 'loading');
		updateImportStats('customers');
		loadCustomers(custPage);
	} else {
		updateImportUI('customers', 'success');
		updateImportStats('customers'); 
	}

	if (!ordersLoaded) {
		updateImportUI('orders', 'loading');
		updateImportStats('orders');
		loadOrders(ordersPage);
	} else {
		updateImportUI('orders', 'success');
		updateImportStats('orders');
	}

	checkImportCompletion();
}

function loadCustomers(page) {
	$.ajax({
		url: '<?php echo $load_customers; ?>',
		type: 'post',
		data: { page: page },
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				custRetries = 0; 
				if (json['total_customers']) {
					customersStats.total = parseInt(json['total_customers'], 10);
				}
				if (json['processed_count']) {
					customersStats.synced += parseInt(json['processed_count'], 10);
				}
				if (json['failed_count']) {
					customersStats.failed += parseInt(json['failed_count'], 10);
				}
				
				customersStats.success = Math.max(0, customersStats.synced - customersStats.failed);
				updateImportStats('customers');

				if (json['next_page']) {
					loadCustomers(json['next_page']);
				} else {
					isCustomersDone = true;
					custLoaded = true;
					updateImportUI('customers', 'success');
					customersStats.synced = customersStats.total;
					customersStats.success = Math.max(0, customersStats.total - customersStats.failed);
					updateImportStats('customers');
					checkImportCompletion();
				}
			} else {
				custRetries++;
				if (custRetries <= maxRetries) {
					setTimeout(function() { loadCustomers(page); }, 3000);
				} else {
					handleImportError('customers', '<?php echo $error_connection; ?>');
				}
			}
		},
		error: function() {
			custRetries++;
			if (custRetries <= maxRetries) {
				setTimeout(function() { loadCustomers(page); }, 3000);
			} else {
				handleImportError('customers', '<?php echo $error_connection; ?>');
			}
		}
	});
}

function loadOrders(page) {
	$.ajax({
		url: '<?php echo $load_orders; ?>',
		type: 'post',
		data: { page: page },
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				ordersRetries = 0; 
				if (json['total_orders']) {
					ordersStats.total = parseInt(json['total_orders'], 10);
				}
				if (json['processed_count']) {
					ordersStats.synced += parseInt(json['processed_count'], 10);
				}
				if (json['failed_count']) {
					ordersStats.failed += parseInt(json['failed_count'], 10);
				}
				
				ordersStats.success = Math.max(0, ordersStats.synced - ordersStats.failed);
				updateImportStats('orders');

				if (json['next_page']) {
					loadOrders(json['next_page']);
				} else {
					isOrdersDone = true;
					ordersLoaded = true;
					updateImportUI('orders', 'success');
					ordersStats.synced = ordersStats.total;
					ordersStats.success = Math.max(0, ordersStats.total - ordersStats.failed);
					updateImportStats('orders');
					checkImportCompletion();
				}
			} else {
				ordersRetries++;
				if (ordersRetries <= maxRetries) {
					setTimeout(function() { loadOrders(page); }, 3000);
				} else {
					handleImportError('orders', '<?php echo $error_connection; ?>');
				}
			}
		},
		error: function() {
			ordersRetries++;
			if (ordersRetries <= maxRetries) {
				setTimeout(function() { loadOrders(page); }, 3000);
			} else {
				handleImportError('orders', '<?php echo $error_connection; ?>');
			}
		}
	});
}

$('#step-tracking .btn-retry-step').on('click', function() {
	getSiteScript(1);
});

$('#step-push .btn-retry-step').on('click', function() {
	addWebPush(1);
});

$('#step-import .btn-retry-import').on('click', function() {
	$('.import-error-msg').hide().empty();
	setStepStatus('step-import', 'loading');
	
	if (custFailed) {
		custFailed = false;
		custRetries = 0;
		updateImportUI('customers', 'loading');
		loadCustomers(custPage);
	}
	if (ordersFailed) {
		ordersFailed = false;
		ordersRetries = 0;
		updateImportUI('orders', 'loading');
		loadOrders(ordersPage);
	}
});
</script>
<?php echo $footer; ?>