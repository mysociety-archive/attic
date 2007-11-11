<fieldset>
    <input type="hidden" id="hidSaveMapData" value="0" />
    <input type="hidden" id="hidMiniMap" value="{$mini_map}" />        
</fieldset>
<div id="divGroup" {if $preview == true}class="preview"{/if}>
    <div id="divGroupHeader">
        <h3>{$group->name}</h3>
        {if $preview != true}
            <div id="divInvolved">
                {if $group->involved_type == 'email'}
                    {if !$dead_links}
                        <a href="{$www_server}/groups/{$group->url_id}/contact/">Join this group</a>
                    {else}
                        <a href="#" title="link disabled for preview">Join this group</a>
                    {/if}
                {else}
                    {if !$dead_links}
                        <a href="{$group->involved_link}">Join this group</a>
                    {else}
                        <a href="#" title="link disabled for preview">Join this group</a>
                    {/if}
                {/if}
            </div>
        {/if}
    </div>

    <div id="divDescription">
        <h4>{$group->byline}</h4>
        {$group->description}
        <div id="divMapMiniWrapper">
            <div id="divMap"></div>
            <small>(approximate area for this group)</small>
        </div>
    </div>

    <div id="divMeta">
        <em>{$group->name}</em> has been tagged with the keywords <strong>{$group->tags}</strong>.
        This page was created by <strong>{$group->created_name} on {$group->created_date|date_format}</strong>. 
        {if !$dead_links}
            <a href="{$www_server}/groups/{$group->url_id|escape:url}/report/">Suggest a change to this page</a>.
        {/if}
    </div>
</div>