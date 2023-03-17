
var $ = (typeof $ === 'undefined')?jQuery.noConflict():$;

const tabSystem = {
    init() {
        document.querySelectorAll('.tabs-menu').forEach((tabMenu) => {
            Array.from(tabMenu.children).forEach((child, ind) => {
                child.addEventListener('click', () => {
                    tabSystem.toggle(child.dataset.target);
                });
                if (child.className.includes('is-active')) {
                    tabSystem.toggle(child.dataset.target);
                }
            });
        });
    },
    toggle(targetId) {
        document.querySelectorAll('.tab-content').forEach((contentElement) => {
            contentElement.style.display =
                contentElement.id === targetId ? 'block' : 'none';
            document
                .querySelector(`[data-target="${contentElement.id}"]`)
                .classList[contentElement.id === targetId ? 'add' : 'remove'](
                    'is-active'
                );
        });
    },
};

function copyElement(element) {
	copyText(element.innerText);
}

function copyText(textToCopy) {

    var myTemporaryInputElement = document.createElement("input");
    myTemporaryInputElement.type = "text";
    myTemporaryInputElement.value = textToCopy;

    document.body.appendChild(myTemporaryInputElement);

    myTemporaryInputElement.select();
    document.execCommand("Copy");

    document.body.removeChild(myTemporaryInputElement);

}

function genRandomInt(max) {
	let lgMax=max.toString().length - 1;
    let res = Math.floor(Math.random() * max) + 1;
	let strRes=res.toString();
	
	if(strRes.length < lgMax)
		strRes = "0".repeat(lgMax - strRes.length) + strRes;
	
	return strRes;
}

function initAccordion(selector,option) {
	$(".a-container").not(".active").find(".a-panel").hide();
	$(selector).find(".a-btn").click(function(e) {
		// if($(this).closest(".a-container").hasClass("active")) {
			$(this).closest(".a-container").find(".a-panel").slideToggle("slow")
			$(this).closest(".a-container").toggleClass("active");
		// }
	});
	
}

// Functions to open and close a modal
function openModal(el) {
	el.classList.add('is-active');
}

function closeModal(el) {
	el.classList.remove('is-active');
}

function closeAllModals() {
	(document.querySelectorAll('.modal') || []).forEach(($modal) => {
	  closeModal($modal);
	});
}

function closeLoader() {
	$(".preloader.is-loading").removeClass("is-loading");
}

window.addEventListener('beforeunload', (e) => {
  $(".preloader").addClass("is-loading");

});
	
window.addEventListener('load', () => {
	$(".preloader.is-loading").removeClass("is-loading");
});

document.addEventListener('DOMContentLoaded', () => {
	$(".preloader").addClass("is-loading");

  // Get all "navbar-burger" elements
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  // Add a click event on each of them
  $navbarBurgers.forEach( el => {
    el.addEventListener('click', () => {

      // Get the target from the "data-target" attribute
      const target = el.dataset.target;
      const $target = document.getElementById(target);

      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      el.classList.toggle('is-active');
      $target.classList.toggle('is-active');

    });
  });

  // Add a click event on buttons to open a specific modal
  (document.querySelectorAll('.js-modal-trigger') || []).forEach((trigger) => {
    const modal = trigger.dataset.target;
    const target = document.getElementById(modal);

    trigger.addEventListener('click', () => {
		// $(".preloader").addClass("is-loading");
		openModal(target);
		$(target).trigger( "modal:toggled" );
    });
  });

  // Add a click event on various child elements to close the parent modal
  (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach((close) => {
    const target = close.closest('.modal');

    close.addEventListener('click', () => {
      closeModal(target);
	  $(target).trigger( "modal:toggled" );
    });
  });

  // Add a keyboard event to close all modals
  document.addEventListener('keydown', (event) => {
    const e = event || window.event;

    if (e.keyCode === 27) { // Escape key
      closeAllModals();
    }
  });
  
	// Activate tabs
	tabSystem.init();
	
	initAccordion('.accordion', false);

	$("form.room-edit #access").change((e) => {
		if( $(e.target).find("option:selected").val() == "code") {
			$("form.room-edit").addClass("is-access-code");
		}
		else
			$("form.room-edit").removeClass("is-access-code");
			
	});

	$("form.room-edit .fld-access-code #generate-code").click((e) => {
		$(e.target).closest(".fld-access-code").find("#room_accesscode").val(genRandomInt(1000000));
	});
	
	$("#modal-useraccess").on("modal:toggled", (e) => {

		if($(e.currentTarget).hasClass("is-active"))
		{
			let parent=$(e.currentTarget).find("[data-iframe]");
			
			if(parent.length > 0) {
				$(".preloader").addClass("is-loading");
				// parent.find("iframe").remove();
				// parent.append(parent.data("iframe"));
				parent.html('<iframe src="' + parent.data("iframe") + '" onload="closeLoader();" />');
			}

		}
		else {
			//location.reload();
			const params = new URLSearchParams(location.search);
			params.set("active_tab","privileges-tab");
			if(location.hash == '#userlist')
				location.reload();
			else
				location.href = location.origin + location.pathname + '?' + params.toString() + '#userlist';
		}
	});
	

	$(".message button.delete").click((e) =>{
		$(e.target).closest(".message").remove();
	});
	
	if($(".autohide").length > 0) {
		setTimeout(() => {
			$(".autohide").remove();
		},4000);
	}
	
	$("input[type=checkbox].act-as-radio").change(function(e) {
		if(this.checked)
		{
			$("input[type=checkbox].act-as-radio").not(this).prop("checked",false);
			$("input[type=checkbox].act-as-radio").not(this).closest(".panel-heading").removeClass("has-background-success");
			$(this).closest(".panel-heading").addClass("has-background-success");	
		}
		else
			$(this).closest(".panel-heading").removeClass("has-background-success");
	});

	/* Import galene room attributes from json file */
	$('#import-srv-json').change((e) => {
		console.log(e.target.files);
		if(!e.target.files || e.target.files.length <= 0 ) return;

		const reader = new FileReader();
		reader.addEventListener('load', (event) => {
			var data=JSON.parse(event.target.result);

			var fname=e.target.files[0].name.replace(/\.[^/.]+$/, "");
			$('#galene_group').val(fname);

			if(data.description)
				$('#description').val(data.description);
			if(data.displayName)
				$('#displayName').val(data.displayName);
			if(data.authKeys && Array.isArray(data.authKeys)) {
				for (var i = 0; i < data.authKeys.length; i++) { 
					if(data.authKeys[i].kid && data.authKeys[i].kid == 'gmgr2023' && data.authKeys[i].k) {
						$('#key64').val(data.authKeys[i].k);
					}
				}
			}
			var maxClients=(data['max-clients'])?data['max-clients']:0;
			$('#max_clients').val(maxClients);

			var checked=(data['allow-anonymous'] && data['allow-anonymous'] === true)?true:false;
			$('#allow_anonymous').prop('checked',checked);

			checked=(data['allow-subgroups'] && data['allow-subgroups'] === true)?true:false;
			$('#allow_subgroups').prop('checked',checked);

			checked=(data['allow-recording'] && data['allow-recording'] === true)?true:false;
			$('#allow_recording').prop('checked',checked);

			checked=(data['autolock'] && data['autolock'] === true)?true:false;
			$('#autolock').prop('checked',checked);


		  console.log(data);
		});
		
		reader.readAsText(e.target.files[0]);
	  
	});
	
});