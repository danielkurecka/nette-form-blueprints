function getSelectedFormId(root) {
	var result = null;
	root.querySelectorAll('.form-link').forEach(function (el) {
		if (el.classList.contains('form-link-selected')) {
			result = el.id;
		}
	});
	return result;
}

function getSelectedTemplateName(root) {
	var select = root.querySelector('.template-select');
	return select.options[select.selectedIndex].text;
}

function copyToClipboard(root, element) {
	var textArea = document.createElement('textarea');
	textArea.style.position = 'fixed';
	textArea.value = element.textContent;
	root.appendChild(textArea);
	textArea.select();
	var result = true;
	try {
		document.execCommand('copy');
	} catch (err) {
		result = false;
	}
	root.removeChild(textArea);

	if (result) {
		Array.from(shadow.querySelectorAll('.tooltip-copied')).forEach(function (tooltip) {
			tooltip.hidden = false;
			window.setTimeout(function () {
				tooltip.hidden = true;
			}, 650);
		});
	}
	return result;
}

function isolateToShadow(templateElement) {
	var host = document.createElement('div');
	host.style.all = 'initial';
	host.style.display = 'flex';
	host.style.overflow = 'hidden';
	host.style.flex = '1';
	templateElement.before(host);
	var shadow = host.attachShadow({'mode': 'open'});
	shadow.append(templateElement.content.cloneNode(true));
	return shadow;
}

function prismHighlight(root) {
	var iframe = root.querySelector('.prism-iframe')
	if (iframe.contentWindow.Prism) {
		iframe.contentWindow.Prism.highlightAllUnder(root);
	}
}

function reload(root, templateOptions = {}, useValuesFromServer = false) {
	var previewTabActive = root.querySelector('.tab-selected').dataset.tabName === 'preview';
	var params = {
		formId: useValuesFromServer ? null : getSelectedFormId(root),
		templateName: useValuesFromServer ? null : getSelectedTemplateName(root),
		templateOptions: templateOptions,
		renderPreview: previewTabActive,
	};

	var tracyRefreshOld = window.TracyAutoRefresh;
	window.TracyAutoRefresh = false;
	if (window.Tracy && window.Tracy.Debug && window.Tracy.Debug.setOptions) {
		window.Tracy.Debug.setOptions({autoRefresh: false})
	}

	var spinner = root.querySelector('.spinner');
	spinner.hidden = false;
	window.fetch(window.location.href, {headers: {'X-Daku-Nette-Form-Blueprints-Ajax': JSON.stringify(params)}}).then(function (response) {
		return response.text();

	}).then(function (text) {
		var lines = text.split('\n');
		var data = JSON.parse(lines[lines.length - 1]);

		if (data['error']) {
			console.log('Ajax error:', data['error']);

		} else {
			root.querySelectorAll('.form-link-list .form-link').forEach(el => el.classList.remove('form-link-selected'));
			root.querySelector('#' + data['formId']).classList.add('form-link-selected');
			root.querySelector('.template-select').value = data['templateName'];
			root.querySelector('.template-options').innerHTML = data['templateOptions'];
			root.querySelector('.html-editor-link').setAttribute('href', data['blueprintFileEditorUri']);
			root.querySelector('.detail-latte pre code').innerHTML = data['latte'];
			root.querySelector('.select-range-list').innerHTML = data['selectRangeListHtml'];
			root.querySelector('.detail-css pre code').textContent = data['styles'];
			setIframePreview(root, data['preview']);
			root.querySelector('.detail-preview').dataset.rendered = previewTabActive ? '1' : '0';
			addCommonListeners(root);
			prismHighlight(root);
		}
		spinner.hidden = true;
	});

	window.TracyAutoRefresh = tracyRefreshOld;
	if (window.Tracy && window.Tracy.Debug && window.Tracy.Debug.setOptions) {
		window.Tracy.Debug.setOptions({autoRefresh: tracyRefreshOld})
	}
}

function setIframePreview(root, previewContent) {
	var iframe = document.createElement('iframe')
	iframe.srcdoc = previewContent;
	iframe.scrolling = 'no';
	iframe.onload = () => iframe.style.height = iframe.contentWindow.document.documentElement.scrollHeight + 'px';
	var previewElement = root.querySelector('.detail-preview div');
	previewElement.innerHTML = '';
	previewElement.appendChild(iframe);
}

function addCommonListeners(root) {
	// template options changing
	root.querySelectorAll('.input-option').forEach(function (el) {
		el.addEventListener('change', function (e) {
			var value = el.type == 'checkbox' ? el.checked : el.value;
			reload(shadow, {[el.name]: value});
		});
	});

	// copy to clipboard buttons
	shadow.querySelectorAll('.copy-button').forEach(function (el) {
		el.addEventListener('click', function (e) {
			e.preventDefault();
			copyToClipboard(shadow, el.closest('.detail').querySelector('pre'));
		});
	});

	// select individual items
	root.querySelector('.select-range-list').addEventListener('click', function (e) {
		var target = e.target;
		if (target.nodeName === 'A') {
			var rangeElement = root.querySelectorAll('.select-range').item(target.getAttribute('data-index'));
			rangeElement.scrollIntoView({behavior: 'smooth', block: 'center'});
			var range = document.createRange();
			range.selectNodeContents(rangeElement);
			var selection = window.getSelection();
			selection.removeAllRanges();
			selection.addRange(range);
			e.preventDefault();
		}
	});
}

var panel = document.querySelector('#tracy-debug-panel-Daku-Nette-FormBlueprints-BlueprintsPanel');

if (panel.querySelector('.tracy-inner') && !panel.dataset.rendered) {
	panel.dataset.rendered = '1';
	// put panel to shadow element (for isolated styles)
	var shadow = isolateToShadow(panel.querySelector('template'));

	// form switching
	shadow.querySelectorAll('.form-link').forEach(function (el) {
		el.addEventListener('click', function (e) {
			el.closest('.form-link-list').querySelectorAll('.form-link').forEach(el => el.classList.remove('form-link-selected'));
			el.classList.add('form-link-selected');
			reload(shadow);
		});
	});

	// template switching
	shadow.querySelector('.template-select').addEventListener('change', (e) => reload(shadow));

	addCommonListeners(shadow);

	// tab detail switching
	shadow.querySelectorAll('.tab').forEach(function (el) {
		el.addEventListener('click', function (e) {
			el.closest('.tab-row').querySelectorAll('.tab').forEach(el => el.classList.remove('tab-selected'));
			el.classList.add('tab-selected');
			shadow.querySelectorAll(el.dataset.targetHide).forEach(el => el.hidden = true);
			shadow.querySelectorAll(el.dataset.targetShow).forEach(el => el.hidden = false);
			window.localStorage.setItem('form-blueprints-last-tab-name', el.dataset.tabName);
			var previewRendered = parseInt(shadow.querySelector('.detail-preview').dataset.rendered)
			if (el.dataset.tabName === 'preview' && !previewRendered) {
				reload(shadow);
			}
		});
	});

	// show last tab
	var lastTabName = window.localStorage.getItem('form-blueprints-last-tab-name') || 'latte';
	shadow.querySelector('.tab[data-tab-name=' + lastTabName).classList.add('tab-selected');
	shadow.querySelector('.detail-' + lastTabName).hidden = false;

	var windowMode = panel.classList.contains('tracy-mode-window');
	if (lastTabName === 'preview' || windowMode) {
		reload(shadow, {}, windowMode);
	}

	// syntax highlighting using Prism
	var prismIframe = shadow.querySelector('.prism-iframe');
	prismIframe.onload = () => prismHighlight(shadow);
	prismIframe.srcdoc = prismIframe.getAttribute('data-srcdoc');
	prismIframe.removeAttribute('data-srcdoc');

} else {
	var ajaxPanelInners = document.querySelectorAll('[id^="tracy-debug-panel-Daku-Nette-FormBlueprints-BlueprintsPanel-ajax"] .tracy-inner');
	for (var inner of ajaxPanelInners) {
		inner.textContent = 'Not available for ajax requests.';
	}
}
