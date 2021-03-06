<fieldset>
    <input type="hidden" id="hidSaveMapData" value="0" />
    <input type="hidden" id="hidMiniMap" value="{$mini_map}" />        
</fieldset>
<div id="divGroup" {if $preview == true}class="preview"{/if}>
    <div id="divGroupHeader">
        <h3>{$group->name}</h3>
        <h4>{$group->byline}</h4>
        {if $preview != true}
            <div id="divInvolved">
                {if $group->involved_type == 'email'}
                    {if !$dead_links}
                        <a class="linkbutton" href="{$www_server}/groups/{$group->url_id}/contact/">
                            <span class="left">&nbsp;</span>
                            <span class="middle">{l}Contact this group{/l} &raquo;</span>                
                            <span class="right">&nbsp;</span>
                        </a>
                    {else}
                        <a class="linkbutton" href="#" title="link disabled for preview">
                            <span class="left">&nbsp;</span>
                            <span class="middle">{l}Contact this group{/l} &raquo;</span>                
                            <span class="right">&nbsp;</span>
                        </a>
                    {/if}
                {else}
                    {if !$dead_links}
                        <a class="linkbutton" href="{$group->involved_link}">
                            <span class="left">&nbsp;</span>
                            <span class="middle">{l}Explore or join this group{/l} &raquo;</span>                
                            <span class="right">&nbsp;</span>
                        </a>
                    {else}
                        <a class="linkbutton" href="#" title="link disabled for preview">
                            <span class="left">&nbsp;</span>
                            <span class="middle">{l}Explore or join this group{/l} &raquo;</span>                
                            <span class="right">&nbsp;</span>
                        </a>
                    {/if}
                {/if}
            </div>
        {/if}
    </div>

    <div id="divDescription">
        <div id="divDescriptionText">
            {if $description}{$description}{else}{$group->description}{/if}
            <div id="divMeta">
                {l}<em>{$group->name}</em>{if $category} is a <strong>{$category->name|lower} group</strong> and{/if} has been tagged with the keywords <strong>{$group->tags}</strong>.{/l}
                {l}This page was created by <strong>{if $group->created_name == ''}anonymous{else}{$group->created_name}{/if} on {$group->created_date|date_format}</strong>. 
                    {if !$dead_links}
                        <a href="{$www_server}/groups/{$group->url_id|escape:url}/report/">Suggest a change</a> or
                        <a href="{$www_server}/groups/{$group->url_id|escape:url}/edit/">Edit this page</a> (creator only)
                    {/if}
                {/l}
            </div>
        </div>
        <div id="divMapMiniWrapper">
            <div id="divMap"></div>
            <small>({l}approximate area for this group{/l})</small>
        </div>
    </div>

</div>
