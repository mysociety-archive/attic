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
                        <a href="{$www_server}/groups/{$group->url_id}/contact/">{l}Join this group{/l}</a>
                    {else}
                        <a href="#" title="link disabled for preview">{l}Join this group{/l}</a>
                    {/if}
                {else}
                    {if !$dead_links}
                        <a href="{$group->involved_link}">{l}Explore or join this group{/l}</a>
                    {else}
                        <a href="#" title="link disabled for preview">{l}Explore or join this group{/l}</a>
                    {/if}
                {/if}
            </div>
        {/if}
    </div>

    <div id="divDescription">
        <div id="divDescriptionText">
            {if $description}{$description}{else}{$group->description}{/if}
        </div>
        <div id="divMapMiniWrapper">
            <div id="divMap"></div>
            <small>({l}approximate area for this group{/l})</small>
        </div>

        <div id="divMeta">
            {l}<em>{$group->name}</em> has been tagged with the keywords <strong>{$group->tags}</strong>.{/l}
            {l}This page was created by <strong>{$group->created_name} on {$group->created_date|date_format}</strong>. 
                {if !$dead_links}
                    <a href="{$www_server}/groups/{$group->url_id|escape:url}/report/">Suggest a change to this page</a>.
                {/if}
            {/l}
        </div>

    </div>
</div>
