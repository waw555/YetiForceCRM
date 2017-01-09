{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
<div class="SendEmailFormStep2" name="emailPreview">
	<input type="hidden" name="parentRecord" value="{$PARENT_RECORD}"/>
	<input type="hidden" name="recordId" value="{$RECORD_ID}"/>
	<br>
	<div class="well well-large zeroPaddingAndMargin modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header blockHeader emailPreviewHeader" style="height:30px">
					<h3 class='col-md-4 modal-title'>{vtranslate('SINGLE_Emails', $MODULE)} {vtranslate('LBL_INFO', $MODULE)}</h3>
					<div class='pull-right'>
						<span class="btn-toolbar">
							<span class="btn-group">
								<button type="button" name="previewForward" class="btn btn-default" data-mode="emailForward">
									<strong>{vtranslate('LBL_FORWARD',$MODULE)}</strong>
								</button>
							</span>
							{if !($RECORD->isSentMail())}
								<span class="btn-group">
									<button type="button" name="previewEdit" class="btn btn-default" data-mode="emailEdit">
										<strong>{vtranslate('LBL_EDIT',$MODULE)}</strong>
									</button>
								</span>
							{/if}
							<span class="btn-group">
								<button type="button" name="previewPrint" class="btn btn-default" data-mode="previewPrint">
									<strong>{vtranslate('LBL_PRINT',$MODULE)}</strong>
								</button>
							</span>
						</span>
					</div>
				</div>
				<form class="form-horizontal emailPreview">
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_FROM',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								<span class="row">{$FROM}</span>
							</span>
						</span>
					</div>
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_TO',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								{if empty($TO)}
									{assign var=TO value=array()}
								{/if}
								{assign var=TO_EMAILS value=","|implode:$TO}
								<span class="row">{$TO_EMAILS}</span>
							</span>
						</span>
					</div>
					{if !empty($CC)}
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_CC',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								<span class="row">
									{$CC}
								</span>
							</span>
						</span>
					</div>
					{/if}
					{if !empty($BCC)}
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_BCC',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								<span class="row">
									{$BCC}
								</span>
							</span>
						</span>
					</div>
					{/if}
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_SUBJECT',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								<span class="row">
									{$RECORD->get('subject')}
								</span>
							</span>
						</span>
					</div>
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_ATTACHMENT',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								<span class="row">
									{foreach item=ATTACHMENT_DETAILS  from=$RECORD->getAttachmentDetails()}
										<a href="index.php?module=Documents&action=DownloadFile&record={$ATTACHMENT_DETAILS['docid']}">
											{$ATTACHMENT_DETAILS['attachment']}
										</a>&nbsp;&nbsp; 
									{/foreach}
								</span>
							</span>
						</span>
					</div>
					<div class="row padding-bottom1per">
						<span class="col-md-12 row">
							<span class="col-md-2">
								<span class="pull-right muted">{vtranslate('LBL_DESCRIPTION',$MODULE)}</span>
							</span>
							<span class="col-md-9">
								<span class="row">
									{decode_html($RECORD->get('description'))}
								</span>
							</span>
						</span>
					</div>
					<div class="row">
						<span class="col-md-1">&nbsp;</span>
						<span class="col-md-10 margin0px"><hr/></span>
					</div>
					<div class="row">
						<span class="col-md-4">&nbsp;</span>
						<span class="col-md-4 textAlignCenter">
							<span class="muted">
								{if $RECORD->get('email_flag') eq "SAVED"}
									<small><em>{vtranslate('LBL_DRAFTED_ON',$MODULE)}</em></small>
									<span><small><em>&nbsp;{Vtiger_Util_Helper::formatDateTimeIntoDayString($RECORD->get('createdtime'))}</em></small></span>
														{elseif $RECORD->get('email_flag') eq "MailManager"} 
																<small><em>{vtranslate('LBL_MAIL_DATE',$MODULE)} : </em></small> 
																{assign var="MAIL_DATE" value=$RECORD->get('date_start')|@cat:' '|@cat:$RECORD->get('time_start')} 
																<span><small><em>&nbsp;{Vtiger_Util_Helper::formatDateTimeIntoDayString($MAIL_DATE)}</em></small></span> 
								{else}
									<small><em>{vtranslate('LBL_SENT_ON',$MODULE)}</em></small>
									{assign var="SEND_TIME" value=$RECORD->get('date_start')|@cat:' '|@cat:$RECORD->get('time_start')}
									<span><small><em>&nbsp;{Vtiger_Util_Helper::formatDateTimeIntoDayString($SEND_TIME)}</em></small></span>
								{/if}
							</span>
						</span>
					</div>
					<div class="row">
						<span class="col-md-3">&nbsp;</span>
						<span class="col-md-5 textAlignCenter">
							<span><strong> {vtranslate('LBL_OWNER',$MODULE)} : {\App\Fields\Owner::getLabel($RECORD->get('assigned_user_id'))}</strong></span>
						</span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{include file='JSResources.tpl'|vtemplate_path}
{/strip}
