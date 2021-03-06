{include file='header' pageTitle='chat.acp.room.'|concat:$action}

<script type="text/javascript" src="{@$__wcf->getPath('wcf')}js/WCF.ACL.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function() {
		new WCF.ACL.List($('#groupPermissions'), {@$objectTypeID}, ''{if $roomID|isset}, {@$roomID}{/if});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}chat.acp.room.{$action}{/lang}</h1>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.form.{$action}.success{/lang}</p>
{/if}

<div class="contentNavigation">
	{hascontent}
		<nav>
			<ul>
				{content}
					<li><a href="{link application='chat' controller='RoomList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}chat.acp.menu.link.room.list{/lang}</span></a></li>
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

<form method="post" action="{if $action == 'add'}{link application='chat' controller='RoomAdd'}{/link}{else}{link application='chat' controller='RoomEdit' id=$roomID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>
			
			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}chat.acp.room.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$title}" autofocus="autofocus" class="long" />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}chat.acp.room.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}
			
			<dl{if $errorField == 'topic'} class="formError"{/if}>
				<dt><label for="topic">{lang}chat.acp.room.topic{/lang}</label></dt>
				<dd>
					<input type="text" id="topic" name="topic" value="{$topic}" class="long" />
					{if $errorField == 'topic'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}chat.acp.room.topic.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			{include file='multipleLanguageInputJavascript' elementIdentifier='topic' forceSelection=false}
			
			<dl id="groupPermissions">
				<dt>{lang}wcf.acl.permissions{/lang}</dt>
				<dd></dd>
			</dl>
		</fieldset>
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
	</div>
</form>

{include file='footer'}