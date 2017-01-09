{strip}
	{*<!-- {[The file is published on the basis of YetiForce Public License that can be found in the following directory: licenses/License.html]} --!>*}
	<div class="widget_header row">
		<div class="col-md-8">
			{include file='BreadCrumbs.tpl'|@vtemplate_path:$MODULE}
			{if isset($SELECTED_PAGE)}
				{App\Language::translate($SELECTED_PAGE->get('description'),$QUALIFIED_MODULE)}
			{/if}
		</div>
		<div class="col-md-4 marginbottomZero">
			<div class="pull-right btn-toolbar"><span class="actionImages">
					<a class="btn btn-info" href="{$RECORD_MODEL->getEditViewUrl()}">
						<strong>{App\Language::translate('LBL_EDIT_RECORD', $QUALIFIED_MODULE)}</strong>
					</a>
					<a class="btn btn-danger marginLeft5" href="{$RECORD_MODEL->getDeleteActionUrl()}">
						<strong>{App\Language::translate('LBL_DELETE_RECORD', $QUALIFIED_MODULE)}</strong>
					</a>
			</div>
		</div>
	</div>
	<div class="detailViewInfo">
		<table class="table table-bordered">
			<thead>
				<tr class="blockHeader">
					<th colspan="2" class="col-md-12"><strong>{App\Language::translate('LBL_SMTP_DETAIL',$QUALIFIED_MODULE)}</strong></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_NAME', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('name')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_MAILER_TYPE', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('mailer_type')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_HOST', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('host')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_PORT', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('port')}
					</td>
				</tr>
					<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_AUTHENTICATION', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('authentication')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_USERNAME', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('username')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_PASSWORD', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('password')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_SECURE', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('secure')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_INDIVIDUAL_DELIVERY', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('individual_delivery')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_FROM_EMAIL', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('from_email')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_FROM_NAME', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('from_name')}
					</td>
				</tr>
				<tr>
					<td class="col-md-3" ><label class="pull-right">{App\Language::translate('LBL_REPLAY_TO', $QUALIFIED_MODULE)}</label></td>
					<td class="col-md-8">
						{$RECORD_MODEL->getDisplayValue('replay_to')}
					</td>
				</tr>
		

			</tbody>
		</table>
	</div>
	{strip}
