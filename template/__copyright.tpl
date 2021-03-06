{if $__chat->isActiveApplication()}
	{if $templateName != '__copyright'}<address id="timsChatCopyright" class="copyright marginTop">{lang}chat.general.copyright{/lang}</address>
	{elseif $templateName == '__copyright'}
		<div style="background-image: url('data:image/png;base64,{$background}');">
			<dl>
				<dt>{lang}chat.general.copyright.leader{/lang}</dt>
				<dd>
					<ul>
						<li><a href="http://tims.bastelstu.be/">Tim D&uuml;sterhus</a></li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>{lang}chat.general.copyright.developer{/lang}</dt>
				<dd>
					<ul>
						<li><a href="http://tims.bastelstu.be/">Tim D&uuml;sterhus</a></li>
						<li><a href="https://github.com/max-m">Maximilian Mader</a></li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>{lang}chat.general.copyright.graphics{/lang}</dt>
				<dd>
					<ul>
						<li><a href="http://www.cls-design.com/">Tom</a></li>
					</ul>
				</dd>
			</dl>
			{*<dl>
				<dt>{lang}chat.general.copyright.translation{/lang}</dt>
				<dd>

				</dd>
			</dl>*}
			<dl>
				<dt>{lang}chat.general.copyright.thanks{/lang}</dt>
				<dd>
					<ul>
						<li><a href="http://www.wbbaddons.de/user/2020-noone/">-noone-</a></li>
						<li><a href="https://github.com/Gabbid">Gabi</a></li>
						<li><a href="https://github.com/Leon-">Stefan Hahn</a></li>
						<li><a href="http://www.wbbaddons.de">Martin Schwendowius</a></li>
					</ul>
				</dd>
			</dl>
		</div>
	{/if}
{/if}