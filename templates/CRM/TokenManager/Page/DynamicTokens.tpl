<div class="crm-block">
  <div class="crm-block">
    <a href="{crmURL p='civicrm/api4#/explorer/DynamicToken/create' q=""}" class="button">Create new dynamic token</a>
  </div>

  <div class="clear"></div>

  {if $tokens|@count > 0}
    <table class="row-highlight">
      <thead>
        <tr>
          <th class="sorting_disabled">id</th>
          <th class="sorting_disabled">entity</th>
          <th class="sorting_disabled">field</th>
          <th class="sorting_disabled">smarty variable name</th>
          <th class="sorting_disabled">value</th>
          <th class="sorting_disabled">actions</th>
        </tr>
      </thead>
      {foreach from=$tokens item=token}
        <tr class="{cycle values="odd-row,even-row"}">
          <td title="{$token.description}">{$token.id}</td>
          <td title="{$token.description}">{$token.entity_name}</td>
          <td title="{$token.description}">{$token.field_name}</td>
          <td>{$token.smarty_variable_name}</td>
          <td>{$token.value}</td>
          <td>
            <a href="{$token.update_api_link}" target="_blank" class="link">Update</a>
            <a href="{$token.get_api_link}" target="_blank" class="link">Get</a>
            <a data-dynamic-token-id="{$token.id}" href="#" class="remove-dynamic-token link">Delete</a>
          </td>
        </tr>
      {/foreach}
    </table>
  {/if}

  <div class="clear"></div>

  <h3>Available Variables:</h3>

  <div class="crm-block available-variables-content-wrap">
      {foreach from=$availableVariables key=entityName item=variables}
        <label for="{$entityName}">{$entityName}</label>
        <div>
          <textarea id="{$entityName}" cols="80" rows="10" style="text-align: left;">
              {foreach from=$variables item=variable}
                  {'$'}{$variable}
              {/foreach}
          </textarea>
        </div>
      {/foreach}
  </div>

</div>

{literal}
<style>

.available-variables-content-wrap {
  padding: 10px;
}

</style>
{/literal}


{literal}
  <script>
    CRM.$(function ($) {
      initDeletingDynamicTokens();

      function initDeletingDynamicTokens() {
        $('.remove-dynamic-token').click(function () {
          var dynamicTokenId = $(this).data('dynamic-token-id');
          console.log(dynamicTokenId);

          CRM.confirm({
            title: ts('Remove dynamic token'),
            message: ts('Are you sure?'),
          }).on('crmConfirm:yes', function() {

            CRM.api4('DynamicToken', 'delete', {
              where: [["id", "=", dynamicTokenId]]
            }).then(function(results) {
              location.reload();
            }, function(failure) {
              location.reload();
            });
          });
        });
      }
    });
  </script>
{/literal}
