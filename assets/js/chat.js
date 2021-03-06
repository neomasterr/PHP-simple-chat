let sendbtn, inputbox, chatmessages;
let lastmsg = 0;
let login;

document.addEventListener('DOMContentLoaded', function() {
	console.log('Ready');

	login = getCookie('login');

	sendbtn = document.querySelector('button.send');
	chatmessages = document.querySelector('.chat-messages');
	inputbox = document.getElementById('inputbox');

	inputbox.addEventListener('keydown', function(event) {
		if (event.keyCode === 13 && !event.ctrlKey) {
			event.preventDefault();
			sendbtn.click();
		}
	});

	sendbtn.onclick = function() {
		let text = inputbox.value.trim();
		if (!text.length) {
			return;
		}

		inputbox.value = '';

		let params = {
			auth: getCookie('v'),
			message: text
		};

		httpRequest('POST', 'messages.php?act=send', obj_to_formdata(params), function(response, data) {
			let json = JSON.parse(response);

			if (json.error) {
				console.log(json);
			} else if (json.success) {
				// addMessage(params.message, login, true);
				// lastmsg = json.success;
			}
		}, null);
	};

	document.getElementById('chat-title').innerText = 'Чат - ' + getCookie('login');

	setInterval(function(){
		httpRequest('GET', 'messages.php?act=get&from='+lastmsg, null, function(response, data) {
			let json = JSON.parse(response);

			if (json.error) {
				console.log(json);
			}
			else if (json.messages)
			{
				if (json.messages.length > 0)
				{
					for (let i = json.messages.length - 1; i >= 0; --i) {
						addMessage(json.messages[i].message, json.messages[i].login, login == json.messages[i].login);
					}

					chatmessages.scroll(0, chatmessages.scrollHeight)

					lastmsg = json.messages[0].id;
				}
			}
		}, null);
	}, 1000);
});

function addMessage(message, username, own) {
	own = own || false;

	let div = document.createElement('div');
	div.className = 'message';
	if (own) div.classList.add('own');

	let sender = document.createElement('div');
	sender.className = 'sender';
	sender.innerText = username;

	let text = document.createElement('div');
	text.className = 'text';
	text.innerText = message;

	div.appendChild(sender);
	div.appendChild(text);
	chatmessages.appendChild(div);
}

function httpRequest(method, url, form, callback, data)
{
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = function() 
	{ 
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
			callback(xmlHttp.responseText, data);				
	}
	xmlHttp.open(method, url, true);
	xmlHttp.send(form);
}

function obj_to_formdata(obj)
{
	formData = new FormData();
	for (var key in obj)
	{
		formData.append(key, obj[key]);
	}
	return formData;
}

function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}