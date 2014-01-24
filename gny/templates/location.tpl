{include file="../templates/header.tpl"}

<div class="contentfull">
    
    {* Check if gaze is up *}
    {if $gaze_down == false}

        {if $country_code != ''}
            {* We have results! display them *}
            {if $found_places}
                <h3>{l}Please confirm which place you are looking for{/l}</h3>
                <ul id="ulLocationResult" class="nobullets">
                    {foreach name="places" from="$places" item="place"}
                        <li>
                            <a href="{$www_server}/search/{$place.name|escape:url|lower}/{$place.longitude|string_format:'%.3f'},{$place.latitude|string_format:'%.3f'}">{$place.pretty_name}</a>
                        </li>
                    {/foreach}
                </ul>

                {* Search form *}
                <form id="frmSearchPlaceName" action="{$form_action}" method="post">
                    {include file="../templates/formvars.tpl"}
                    <fieldset>
                        <label for="txtSearch">{l}Change location?{/l}</label>
                        <input type="textbox" class="textbox" id="txtSearch" name="q" value="{$search_term|escape:html}"/>
                        <select id="ddlCountry" name="country_code">
                            {foreach name="countries" from="$countries" item="country"}                
                                <option value="{$country->iso}"{if $country_code == $country->iso}selected="selected"{/if}>
                                    {$country->printable_name}
                                </option>
                            {/foreach}
                        </select>
                        <input type="submit" class="button" value="{l}Go{/l}" />
                    </fieldset>
                </form>
            {else}
                {* No places found *}
                <div class="attention">
                    <h3>Sorry, we couldn't find anywhere called {$search_term|escape:html}</h3>
                    <form id="frmSearchAgain" action="{$form_action}" method="post">
                        {include file="../templates/formvars.tpl"}
                        <fieldset>
                            <label for="txtSearch">{l}Try another place name?{/l}</label>
                            <input type="textbox" class="textbox" id="txtSearch" name="q" value="{$search_term|escape:html}"/>
                            <select id="ddlCountry" name="country_code">
                                {foreach name="countries" from="$countries" item="country"}                
                                    <option value="{$country->iso}"{if $country_code == $country->iso}selected="selected"{/if}>
                                        {$country->printable_name}
                                    </option>
                                {/foreach}
                            </select>
                            <input type="submit" class="button" value="Go" />
                        </fieldset>
                    </form>
                </div>
            
            {/if}
        {else}
            <div class="attention">
                <h3>{l}Please choose which country you are in{/l}</h3>
                <form id="frmSearchPlace" action="{$form_action}" method="post">
                    {include file="../templates/formvars.tpl"}
                    <fieldset>
                        <input type="hidden" name="q" value="{$search_term|escape:html}"/>
                        <select id="ddlCountry" name="country_code">
                                <option></option>
                            {foreach name="countries" from="$countries" item="country"}                
                                <option value="{$country->iso}">
                                    {$country->printable_name}
                                </option>
                            {/foreach}
                        </select>
                        <input type="submit" class="button" value="{l}Go{/l}" />
                    </fieldset>
                </form>
            </div>
        {/if}

    {else}

        <div class="attention">
        
            <h3>{l}Unable to search by place name{/l}</h3>
            <p>
                {l}Sorry, searching by place name is currently unavailable. Please try again later, or <a href="{$www_server}">search by post code or zip code</a>{/l}
            </p>
        </div>

    {/if}
</div>
{include file="../templates/footer.tpl"}
