<div id="divGroup" {if $preview == true}class="preview"{/if}>
    <div id="divGroupHeader">
        <h3>{$group->name}</h3>
        <h4>{$group->byline}</h4>
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
        {$group->description}
    </div>

    <div id="divMeta">
        You can 
        {if !$dead_links}
            <a href="javascript:popup_map('{$www_server}/map.php?url_id={$group->url_id|escape:url}');" title="View on a map (new window)">view the <em>approximate</em> area covered by this group on a map</a>.
        {else}
            <a href="#" title="link disabled for preview">view the <em>approximate</em> area covered by this group on a map</a>.
        {/if}
        It has been tagged with the keywords <strong>{$group->tags}</strong>.
        This page was created by <strong>{$group->created_name} on {$group->created_date|date_format}</strong>. 
        {if !$dead_links}
            <a href="{$www_server}/groups/{$group->url_id|escape:url}/report/">Something wrong with this group?</a>
        {/if}
    </div>
</div>