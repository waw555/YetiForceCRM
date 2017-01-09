{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): YetiForce.com
 ********************************************************************************/
-->*}

<table width="100%" cellspacing="0" cellpadding="10" class="table importContents">
	<tr>
		<td>
			<strong>{'LBL_IMPORT_STEP_4'|@vtranslate:$MODULE}:</strong>
		</td>
		<td>
			<span>{'LBL_IMPORT_STEP_4_DESCRIPTION'|@vtranslate:$MODULE}</span>
		</td>
		<td>
            <div id="savedMapsContainer" class="textAlignRight pull-right">
				{include file="Import_Saved_Maps.tpl"|@vtemplate_path:'Import'}
			</div>
        </td>
	</tr>
	<tr>
        <td colspan="3">
			<input type="hidden" name="field_mapping" id="field_mapping" value="" />
			<input type="hidden" name="inventory_field_mapping" id="inventory_field_mapping" value="" />
			<input type="hidden" name="default_values" id="default_values" value="" />
			<table width="100%" cellspacing="0" cellpadding="2" class="listRow table table-bordered table-condensed listViewEntriesTable">
				<thead>
					<tr class="listViewHeaders">
						{if $HAS_HEADER eq true}
						<th width="25%"><a>{'LBL_FILE_COLUMN_HEADER'|@vtranslate:$MODULE}</a></th>
						{/if}
						<th width="25%"><a>{'LBL_ROW_1'|@vtranslate:$MODULE}</a></th>
						<th width="23%"><a>{'LBL_CRM_FIELDS'|@vtranslate:$MODULE}</a></th>
						<th width="27%"><a>{'LBL_DEFAULT_VALUE'|@vtranslate:$MODULE}</a></th>
					</tr>
				</thead>
				<tbody>
					{foreach key=TYPE_NAME item=FIELDS_DATA from=$ROW_1_DATA name="rowData"}
						<tr class=""><td class="textAlignCenter bg-primary" colspan="4">{$TYPE_NAME|@vtranslate:$MODULE}</td></tr>
						{if $smarty.foreach.rowData.iteration gt 1}
							{assign var="TYPE_AVAILABLE_BLOCKS" value=$INVENTORY_BLOCKS}
							{assign var="PREFIX" value='inventory_'}
						{else}
							{assign var="TYPE_AVAILABLE_BLOCKS" value=$AVAILABLE_BLOCKS}
							{assign var="PREFIX" value=''}
						{/if}
						{foreach key=_HEADER_NAME item=_FIELD_VALUE from=$FIELDS_DATA name="headerIterator"}
							{assign var="_COUNTER" value=$smarty.foreach.headerIterator.iteration}
							{if $PREFIX && is_numeric($_HEADER_NAME)} {continue} {/if}
							<tr class="fieldIdentifier" id="fieldIdentifier{$_COUNTER}" data-typename="{$TYPE_NAME}">
								{if $HAS_HEADER eq true}
								<td class="cellLabel">
									<span name="header_name">{$_HEADER_NAME}</span>
								</td>
								{/if}
								<td class="cellLabel">
									<span>{\vtlib\Functions::textLength($_FIELD_VALUE)}</span>
								</td>
								<td class="cellLabel">
									<input type="hidden" name="row_counter" value="{$_COUNTER}" />
									<select name="{$PREFIX}mapped_fields" class="txtBox chzn-select form-control {if $PREFIX}inventory{/if}" onchange="ImportJs.loadDefaultValueWidget('fieldIdentifier{$_COUNTER}')">
										<option value="">{'LBL_NONE'|@vtranslate:$FOR_MODULE}</option>
										{foreach key=BLOCK_NAME item=_FIELDS from=$TYPE_AVAILABLE_BLOCKS}
											<optgroup label="{vtranslate($BLOCK_NAME, $FOR_MODULE)}">
											{foreach key=_FIELD_NAME item=_FIELD_INFO from=$_FIELDS}
												{if $_FIELD_INFO->getFieldDataType() eq 'inventory'}
													{assign var="_TRANSLATED_FIELD_LABEL" value=$_FIELD_INFO->get('label')|@vtranslate:$FOR_MODULE}
												{else}
													{assign var="_TRANSLATED_FIELD_LABEL" value=$_FIELD_INFO->getFieldLabelKey()|@vtranslate:$FOR_MODULE}
												{/if}
												<option value="{$_FIELD_NAME}" {if decode_html($_HEADER_NAME) eq $_TRANSLATED_FIELD_LABEL} selected {/if} data-label="{$_TRANSLATED_FIELD_LABEL}">{$_TRANSLATED_FIELD_LABEL}{if $_FIELD_INFO->isMandatory() eq 'true'}&nbsp; (*){/if}</option>
											{/foreach}
											</optgroup>
										{/foreach}
									</select>
								</td>
								<td class="cellLabel row" name="default_value_container">&nbsp;</td>
							</tr>
						{/foreach}
					{/foreach}
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right" colspan="2">
			<div class="row">
				<div class="col-xs-8">
					<input type="checkbox" title="{'LBL_SAVE_AS_CUSTOM_MAPPING'|@vtranslate:$MODULE}" name="save_map" id="save_map"/>&nbsp;{'LBL_SAVE_AS_CUSTOM_MAPPING'|@vtranslate:$MODULE}&nbsp;&nbsp;
				</div>
				<div class="col-xs-4">
					<input class="form-control" type="text" name="save_map_as" id="save_map_as"/>
				</div>
			</div>
		</td>
	</tr>
</table>
{include file="Import_Default_Values_Widget.tpl"|@vtemplate_path:'Import'}
