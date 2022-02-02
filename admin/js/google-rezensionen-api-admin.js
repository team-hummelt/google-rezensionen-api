(function ($) {
    'use strict';




    /**====================================================
     ================  ADMIN XHR FORMULARE ================
     ======================================================
     */
    function admin_xhr_extension_form_data(data, is_formular = true, callback = NULL) {
        let xhr = new XMLHttpRequest();
        let formData = new FormData();

        if (is_formular) {
            let input = new FormData(data);
            for (let [name, value] of input) {
                formData.append(name, value);
            }
        } else {
            for (let [name, value] of Object.entries(data)) {
                formData.append(name, value);
            }
        }

        formData.append('_ajax_nonce', rezensionen_ajax_obj.nonce);
        formData.append('action', 'HupaGoogleApiHandle');

        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (typeof callback === 'function') {
                    xhr.addEventListener("load", callback);
                    return false;
                } else {
                    let data = JSON.parse(this.responseText);
                    if (data.status) {
                        success_message(data.msg);
                    } else {
                        warning_message('Error: no return');
                    }
                }
            }
        }
        xhr.open('POST', rezensionen_ajax_obj.ajax_url, true);
        xhr.send(formData);
    }

    $(document).on('click', '.btn-collapse', function () {
        let btnAll = $('.btn-collapse');
        btnAll.removeClass('active').prop('disabled', false);
        $(this).addClass('active').prop('disabled', true);
        let currentSideTitleDiv = $('.currentSideTitle');
        let site = $(this).attr('data-bs-site');
        let sideTitle = $(this).attr('data-bs-title');
        switch (site) {
            case 'overview':

                break;
            case'settings':

                break;
        }
        currentSideTitleDiv.html(sideTitle)
    });

    // Datenschutz Check Funktion
    $(document).on('click', '.gMapsCheckDS', function () {
        $(this).trigger('blur');
        let btnDsAccept = $('.btnDatenSchutzLoad');
        $(this).prop('checked') ? btnDsAccept.prop('disabled', false) : btnDsAccept.prop('disabled', true);
    });

    // Back Button zur Startseite
    $(document).on('click', '.cancelDsBtn', function () {
        let overviewTemplates = document.getElementById('overviewTemplates');
        let formDs = document.querySelector('#googleDS .google-api-form');
        document.querySelector('.btnDatenSchutzLoad').setAttribute('disabled', 'disabled')
        formDs.reset();
        new bootstrap.Collapse(overviewTemplates, {
            toggle: true,
            parent: '#googleApiCollapseParent'
        })

        let searchCountriesColl = document.getElementById('countriesSearchColl');
        if (searchCountriesColl) {
            new bootstrap.Collapse(searchCountriesColl, {
                toggle: true,
                parent: '#searchPlaceIDParent'
            })
        }
    });

    // Datenschutz anzeigen
    $(document).on('click', '.btn-show-ds', function () {
        let dsColl = document.getElementById('googleDS');
        new bootstrap.Collapse(dsColl, {
            toggle: true,
            parent: '#googleApiCollapseParent'
        })
    });

    // Karte erstellen Step1
    $(document).on('click', '.btn-load-step1', function () {
        let step1Coll = document.getElementById('showPlaceIdStepOne');
        new bootstrap.Collapse(step1Coll, {
            toggle: true,
            parent: '#googleApiCollapseParent'
        })
        $('.alert-wrapper').remove();
        $('#inputPlaceID').val('');
        $('#placeIDGoogleInputSearchBtn').prev().val('');
        let sendPlaceId = $('#sendPlaceID').prev();
        sendPlaceId.removeClass('is-invalid').val('');
    });

    $(document).on('click', '.radio-update-option', function () {
        let selUpdateInt = $('#updateInterval');
        if ($(this).val() == '2') {
            selUpdateInt.prop('disabled', true);
        } else {
            selUpdateInt.prop('disabled', false);
        }
    });


    /**===========================================================
     ================  REZENSION UPDATE AJAX CALL ================
     =============================================================
     */
    $(document).on('click', '#update_now_all', function () {

        let formData = {
            'method': 'update_rezensionen_by_id',
            'id' : $(this).attr('data-id')
        }
        admin_xhr_extension_form_data(formData, false, update_rezension_callback);
    });

    function update_rezension_callback() {
        let data = JSON.parse(this.responseText);
        if (data.status) {
            success_message(data.msg);
            let formData = {
                'method': 'get_settings_by_id_template',
                'id': data.id
            }
            admin_xhr_extension_form_data(formData, false, render_settings_by_id_aktuell_template_callback);

        } else {
            warning_message(data.msg)
        }
    }

    function render_settings_by_id_aktuell_template_callback() {
        let data = JSON.parse(this.responseText);
        if (data.status) {
            let tempWrapper = $('#twig-settings-template');
            tempWrapper.html(data.template);
            formularAutosaveLoad();
            load_tooltip_by_selector('#update_now_all');
        }
    }

    //Place ID Senden
    $(document).on('click', '#sendPlaceID', function () {
        let input = $(this).prev();
        if (!input.val()) {
            input.addClass('is-invalid');
            return false;
        }

        let formData = {
            'method': 'search_data_by_place_id',
            'search': input.val()
        }
        admin_xhr_extension_form_data(formData, false, search_by_place_id_callback);
    });

    function search_by_place_id_callback() {
        let data = JSON.parse(this.responseText);
        let input = $('#sendPlaceID').prev();
        if (data.status) {
            input.removeClass('is-invalid');
            let searchGoogleTextColl = document.getElementById('resultPlaceId');
            new bootstrap.Collapse(searchGoogleTextColl, {
                toggle: true,
                parent: '#googleApiCollapseParent'
            });

            let template = $('.render-result-place-id');
            template.html(data.template);
        } else {
            if (data.input_false) {
                input.addClass('is-invalid');
            }
            if (data.show_alert) {
                alert(data.msg, 'danger', '#showPlaceIdStepOne .card');
                $('.alert-wrapper').addClass('p-3');
            }
            warning_message(data.msg);
        }
    }


    // Platz ID finden
    $(document).on('click', '.btnSearchPlaceID', function () {
        let searchIdColl = document.getElementById('searchPlaceId');
        new bootstrap.Collapse(searchIdColl, {
            toggle: true,
            parent: '#googleApiCollapseParent'
        });
        let searchCountriesColl = document.getElementById('countriesSearchColl');
        if (searchCountriesColl) {
            let bsShow = new bootstrap.Collapse(searchCountriesColl, {
                toggle: false,
                parent: '#searchPlaceIDParent'
            })
            bsShow.show();
        }
        $('#selectCountry').val('');
        $('.reset-dataset-form').removeClass('active');
    });

    $(document).on('change', '#selectCountry', function () {
        let dataSetClose = $('.reset-dataset-form');
        dataSetClose.addClass('active');
    });


    $(document).on('click', '.reset-dataset-form.active', function (e) {
        $('#selectCountry').val('');
        $(this).removeClass('active');
    })

    $(document).on('click', '#sendCountryData', function (e) {
        let val = $('#selectCountry').val();

        if (!val) {
            return false;
        }
        let inputTypeCountry = $('.inputTypeCountry');
        inputTypeCountry.val(val);
        let searchGoogleTextColl = document.getElementById('searchGoogleTextSearch');
        new bootstrap.Collapse(searchGoogleTextColl, {
            toggle: true,
            parent: '#searchPlaceIDParent'
        })
    });


    /**=====================================================================
     ================  REZENSIONEN SETTINGS BY ID AJAX CALL ================
     =======================================================================
     */
    $(document).on('click', '.btn-card-settings', function (e) {
        let settingsByIdCollapse = document.getElementById('showSettingsById');
        let place_id = $(this).attr('data-id');
        if (!place_id) {
            return false;
        }
        let formData = {
            'method': 'get_settings_by_id_template',
            'id': place_id
        }
        admin_xhr_extension_form_data(formData, false, render_settings_by_id_template_callback);

        function render_settings_by_id_template_callback() {
            let data = JSON.parse(this.responseText);
            if (data.status) {
                new bootstrap.Collapse(settingsByIdCollapse, {
                    toggle: true,
                    parent: '#googleApiCollapseParent'
                })

                let tempWrapper = $('#twig-settings-template');
                tempWrapper.html(data.template);
                formularAutosaveLoad();
                load_tooltip_by_selector('#update_now_all');
            }
        }
    });

    $(document).on('click', '.btn-got-to-overview', function () {
        let formData = {
            'method': 'get_rezensionen_overview'
        }
        admin_xhr_extension_form_data(formData, false, render_overview_template_callback);
    });

    /**===============================================================
     ================  REZENSIONEN OVERVIEW AJAX CALL ================
     =================================================================
     */
    let overviewDataTemplate = document.getElementById('overview-data-template');
    if (overviewDataTemplate) {
        let formData = {
            'method': 'get_rezensionen_overview'
        }
        admin_xhr_extension_form_data(formData, false, render_overview_template_callback);
    }


    function render_overview_template_callback() {
        let data = JSON.parse(this.responseText);
        $('#overview-data-template').html(data.template);
    }

    /**================================================
     ================  SEARCH PLACE ID ================
     ==================================================
     */
    $(document).on('click', '#placeIDGoogleInputSearchBtn', function () {
        let input = $(this).prev();
        if (!input.val()) {
            input.addClass('is-invalid');
        } else {
            input.removeClass('is-invalid');
        }

        let formData = {
            'method': 'find_place_id',
            'search': input.val()
        }

        admin_xhr_extension_form_data(formData, false, result_search_place_id_callback);
    });

    function result_search_place_id_callback() {
        let data = JSON.parse(this.responseText);
        let input = $('#placeIDInputSearchBtn').prev();
        if (data.status) {
            input.removeClass('is-invalid');
            let searchGoogleTextColl = document.getElementById('resultPlaceId');
            new bootstrap.Collapse(searchGoogleTextColl, {
                toggle: true,
                parent: '#googleApiCollapseParent'
            });

            let template = $('.render-result-place-id');
            template.html(data.template);
        } else {
            if (data.input_false) {
                input.addClass('is-invalid');
            }
            if (data.show_alert) {
                alert(data.msg, 'danger', '#searchPlaceId .card');
                $('.alert-wrapper').addClass('p-3');
            }
            warning_message(data.msg);
        }
    }

    /**================================================================
     ================  SEARCH AUTO COMPLETION PLACE ID ================
     ==================================================================
     */
    let AutoCompletionSearchFormTimeout;
    $('.dynamic_forms_input').on('input propertychange change', function () {
        let formData = $(this).closest("form").get(0);
        let input = $('.input_search ', this);
        if (!input.val()) {
            return false;
        }

        clearTimeout(AutoCompletionSearchFormTimeout);
        AutoCompletionSearchFormTimeout = setTimeout(function () {
            admin_xhr_extension_form_data(formData, true, google_search_autocomplete);
        }, 500);
    });


    /**=======================================================
     ================  SEARCH PLACE RESULT OUT================
     =========================================================
     */
    $(document).on('click', '#placeIDGoogleInputSearchBtn', function () {
        let formData = $(this).closest("form").get(0);
        let input = $('.google_place_id ', this);
        if (!input.val()) {
            return false;
        }
        admin_xhr_extension_form_data(formData, true, google_search_place_id_result);

    });

    function google_search_place_id_result() {
        let data = JSON.parse(this.responseText);
    }

    /**=========================================================
     ================  SAVE REZENSIONEN EINTRAG ================
     ===========================================================
     */
    $(document).on('submit', '.set-new-rezension', function (e) {
        let formData = $(this).closest("form").get(0);
        admin_xhr_extension_form_data(formData, true, set_new_rezension_after_callback);
        e.preventDefault();
    });

    function set_new_rezension_after_callback() {
        let data = JSON.parse(this.responseText);
        if (data.status) {
            let searchOverviewColl = document.getElementById('overviewTemplates');
            new bootstrap.Collapse(searchOverviewColl, {
                toggle: true,
                parent: '#googleApiCollapseParent'
            });

            let startMsg = document.getElementById('api-start-message');
            if(startMsg){
                startMsg.remove();
            }
            let rezensionWrapper = document.getElementById('overview-data-template');
            rezensionWrapper.insertAdjacentHTML('beforeend', data.template);
        } else {
            warning_message(data.msg);
        }
    }

    $(document).on('click', '.btn_delete_rezension', function (e) {

        let formData = {
            'method': $(this).attr('data-method'),
            'id': $(this).attr('data-id')
        }
        admin_xhr_extension_form_data(formData, false, delete_rezension_after_callback);

    });

    function delete_rezension_after_callback() {
        let data = JSON.parse(this.responseText);
        if (data.status) {
            let delContainer = $('#target' + data.id);
            delContainer.remove();
            if(data.template){
                $('#overview-data-template').html(data.template);
            }
            success_message(data.msg);
        } else {
            warning_message(data.msg);
        }
    }

    function show_ajax_spinner(data, el = '') {
        let msg = '';
        let ajaxSpinner = document.querySelectorAll(".ajax-status-spinner");
        if(el){
            ajaxSpinner = el;
        }
        if (data.status) {
            msg = '<i class="text-success fa fa-check"></i>&nbsp; Saved! Last: ' + data.msg;
        } else {
            msg = '<i class="text-danger fa fa-exclamation-triangle"></i>&nbsp; ' + data.msg;
        }
        let spinner = Array.prototype.slice.call(ajaxSpinner, 0);
        spinner.forEach(function (spinner) {
            spinner.innerHTML = msg;
        });
    }


    /**=======================================
     ========== Formular Auto-Save  ==========
     =========================================
     */
    formularAutosaveLoad();
    function formularAutosaveLoad() {
        let sendAutoSaveFormTimeout;
        let sendAutoSaveForm = document.querySelectorAll(".google-api-formular-auto-safe:not([type='button'])");
        let ajaxSpinner = document.querySelectorAll(".ajax-status-spinner");
        if (sendAutoSaveForm) {
            let formNodes = Array.prototype.slice.call(sendAutoSaveForm, 0);
            formNodes.forEach(function (formNodes) {
                formNodes.addEventListener("keyup", form_input_ajax_handle, {passive: true});
                formNodes.addEventListener('touchstart', form_input_ajax_handle, {passive: true});
                formNodes.addEventListener('change', form_input_ajax_handle, {passive: true});

                function form_input_ajax_handle(e) {
                    let spinner = Array.prototype.slice.call(ajaxSpinner, 0);
                    spinner.forEach(function (spinner) {
                        spinner.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp; Saving...';
                    });
                    clearTimeout(sendAutoSaveFormTimeout);
                    sendAutoSaveFormTimeout = setTimeout(function () {
                        admin_xhr_extension_form_data(formNodes, true, formular_auto_safe_callback);
                    }, 1000);
                }
            });
        }
    }

    function formular_auto_safe_callback() {
        let data = JSON.parse(this.responseText);
        let responseAlert = document.querySelector('.response-alert');
        if (data.spinner) {
            show_ajax_spinner(data);
            if (data.alert) {
                alert(data.alert_msg, 'danger', '.response-alert');
            } else {
                if (responseAlert) {
                    responseAlert.innerHTML = '';
                }
            }
            return false;
        }

        if(data.opttion_update){
            let optionWrapper = document.querySelector('#target'+data.id+'.target-edit-wrapper');
            let updateTime = optionWrapper.querySelector('.nextUpdate');
            show_ajax_spinner(data);
            updateTime.innerHTML = data.next_time;
        }

        if (data.show_msg) {
            if (data.status) {
                success_message(data.msg);
            } else {
                warning_message(data.msg)
            }
            return false;
        }

        if (data.status) {
            if (data.alert) {
                alert(data.msg, 'success', '.response-alert');
            }
        } else {
            if (data.alert) {
                alert(data.msg, 'danger', '.response-alert');
            }
        }
    }

    let sendGoogleApiForm = document.querySelectorAll(".google-api-form");
    if (sendGoogleApiForm) {
        let formNodes = Array.prototype.slice.call(sendGoogleApiForm, 0);
        formNodes.forEach(function (formNodes) {
            formNodes.addEventListener("submit", function (e) {
                admin_xhr_extension_form_data(formNodes, true, formular_load_api_data_callback);
                e.preventDefault();
            });
        });
    }

    function formular_load_api_data_callback() {
        let data = JSON.parse(this.responseText);
        if (data.status) {
            let mapColl = document.getElementById('showPlaceIdStepOne');
            new bootstrap.Collapse(mapColl, {
                toggle: true,
                parent: '#googleApiCollapseParent'
            });
            let formDs = document.querySelector('#googleDS .google-api-form');
            formDs.reset();

        } else {
            warning_message(data.msg);
        }
    }

    let rezensionInfoModal = document.getElementById('rezensionInfoModal');
    rezensionInfoModal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget
        let place_id = button.getAttribute('data-bs-id');
        let target = button.getAttribute('data-bs-target');
        let formData = {
            'method': 'get_rezension_modal_data',
            'place_id': place_id,
            'target': target
        }
        admin_xhr_extension_form_data(formData, false, get_rezension_modal_data_callback);
    });

    function get_rezension_modal_data_callback() {
        let data = JSON.parse(this.responseText);
        if (data.status) {
            let target = document.querySelector(data.target);
            let label = target.querySelector('#rezensionInfoLabel span');
            let shortcode = target.querySelector('.rezension-shortcode');
            let inputHidden = target.querySelector('.place_id_input');
            let checkAktiv = target.querySelector('#CheckRezensionAktiv');
            label.innerHTML = data.name;
            shortcode.innerHTML = data.shortcode;
            inputHidden.value = data.id;
            if (data.aktiv == '1') {
                checkAktiv.setAttribute('checked', 'checked');
            } else {
                checkAktiv.removeAttribute('checked');
            }

        }
    }

    let rezensionDeleteModal = document.getElementById('rezensionDeleteModal');
    rezensionDeleteModal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget
        let place_id = button.getAttribute('data-bs-id');
        let method = button.getAttribute('data-bs-method');

        let modalButton = rezensionDeleteModal.querySelector('.btn_delete_rezension');
        modalButton.setAttribute('data-method', method);
        modalButton.setAttribute('data-id', place_id);

    });

    function load_tooltip_by_selector(selector) {
        let customTooltip = document.querySelector(selector);
        let tooltip = new bootstrap.Tooltip(customTooltip, {
            boundary: document.body
        });
    }


    const parsedUrl = new URL(window.location.href);
    const getParam = parsedUrl.searchParams.get("page");
    switch (getParam) {
        case 'google-rezensionen-api':
            break;
    }


    /**========================================
     ========== AUTOCOMPLETE FUNTION ==========
     ==========================================
     */
    function google_search_autocomplete() {

        let data = JSON.parse(this.responseText);
        let arr = data.record;
        let inp = document.getElementById("search_completion");
        // autocomplete(document.getElementById("search_completion"), data.record);
        /*the autocomplete function takes two arguments,
        the text field element and an array of possible autocompleted values:*/
        let currentFocus;
        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function (e) {
            let a, b, i, val = this.value;
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            if (!val) {
                return false;
            }
            currentFocus = -1;
            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            /*append the DIV element as a child of the autocomplete container:*/
            this.parentNode.appendChild(a);
            /*for each item in the array...*/
            // for (i = 0; i < arr.length; i++) {
            for (const [key, value] of Object.entries(arr)) {

                //  console.log(val.toUpperCase() + ' --- ' + value.name_adresse.substr(0, val.length).toUpperCase())
                /*check if the item starts with the same letters as the text field value:*/
                // if (value.name_adresse.substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<strong>" + value.name_adresse.substr(0, val.length) + "</strong>";
                b.innerHTML += value.name_adresse.substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + value.name_adresse + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function (e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/

                    let safeInputBtn = document.getElementById('placeIDInputSearchBtn');
                    let html = `<input class="google_place_id" type="hidden" name="place_id" value="${value.place_id}">`;
                    safeInputBtn.insertAdjacentHTML('beforebegin', html);
                    closeAllLists();
                });
                a.appendChild(b);
                //}
                //  }
            }
        });
        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function (e) {
            let x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            console.log(e.keyCode)
            if (e.keyCode == 40) {
                /*If the arrow DOWN key is pressed,
                increase the currentFocus variable:*/
                currentFocus++;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 38) { //up
                /*If the arrow UP key is pressed,
                decrease the currentFocus variable:*/
                currentFocus--;
                /*and and make the current item more visible:*/
                addActive(x);
            } else if (e.keyCode == 13) {
                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                e.preventDefault();
                if (currentFocus > -1) {
                    /*and simulate a click on the "active" item:*/
                    if (x) x[currentFocus].click();
                }
            }
        });

        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            let x = document.getElementsByClassName("autocomplete-items");
            for (let i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }

        /*execute a function when someone clicks in the document:*/
    }

    /*An array containing all the country names in the world:*/
    let countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];

    /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
    //autocomplete(document.getElementById("search_completion"), countries);


})(jQuery);


function copy_data_place_id(e, rand = false) {
    let secretId = e.getAttribute('data-id');
    let el = document.createElement('textarea');
    let supId = '';
    el.value = secretId;

    el.setAttribute('readonly', '');
    el.style = {position: 'absolute', left: '-100vw'};
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    if (rand) {
        supId = rand;
    } else {
        supId = el.value;
    }

    //let info = document.querySelector('#info'+supId);
    let info = jQuery('#info' + supId);
    info.animate({opacity: '1'}, "700");
    info.animate({opacity: '0'}, "9000");
}


// BS-Alert
function alert(message, type, selector) {
    remove_alert()
    let wrapper = document.createElement('div');
    wrapper.classList.add('alert-wrapper');
    let alertPlaceholder = document.querySelector(selector)
    wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert"><i class="fa fa-exclamation-triangle"></i>&nbsp; ' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
    alertPlaceholder.append(wrapper);
}

function remove_alert() {
    let alertWrapper = document.querySelector('.alert-wrapper');
    if (alertWrapper) {
        alertWrapper.remove();
    }
}

//Message Handle
function success_message(msg) {
    let x = document.getElementById("snackbar-success");
    x.innerHTML = msg;
    x.className = "show";
    setTimeout(function () {
        x.className = x.className.replace("show", "");
    }, 4500);
}

function warning_message(msg) {
    let x = document.getElementById("snackbar-warning");
    x.innerHTML = msg;
    x.className = "show";
    setTimeout(function () {
        x.className = x.className.replace("show", "");
    }, 4500);
}


let googleMapsScriptIsInjected = false;
const injectGoogleMapsApiScript = (mapUrl, options = {}) => {
    if (googleMapsScriptIsInjected) {
        //throw new Error('Google Maps Api is already loaded.');
        console.log('Google Maps Api is already loaded.');
        return false;
    }
    const optionsQuery = Object.keys(options)
        .map(k => `${encodeURIComponent(k)}=${encodeURIComponent(options[k])}`)
        .join('&');

    const url = mapUrl + optionsQuery;
    const script = document.createElement('script');
    script.setAttribute('src', url);
    script.setAttribute('async', '');
    script.setAttribute('defer', '');
    document.head.appendChild(script);
    googleMapsScriptIsInjected = true;
};


function initGmapsAutocomplete() {
    const map = new google.maps.Map(document.getElementById("map"), {
        center: {lat: 52.130958, lng: 11.616186},
        zoom: 13,
        mapTypeId: "roadmap",
    });
    // Create the search box and link it to the UI element.
    const input = document.getElementById("pac-input");
    const searchBox = new google.maps.places.SearchBox(input);

    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    // Bias the SearchBox results towards current map's viewport.
    map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
    });

    let markers = [];

    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.
    searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // Clear out the old markers.
        markers.forEach((marker) => {
            marker.setMap(null);
        });
        markers = [];

        // For each place, get the icon, name and location.
        const bounds = new google.maps.LatLngBounds();

        places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
            }

            const icon = {
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(25, 25),
            };

            // Create a marker for each place.
            markers.push(
                new google.maps.Marker({
                    map,
                    icon,
                    title: place.name,
                    position: place.geometry.location,
                })
            );
            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });
}
