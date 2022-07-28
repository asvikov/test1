var bt_minus = document.querySelector('.bt_minus');
var bt_plus = document.querySelector('.bt_plus');
var quantity_el = document.querySelector('.quantity');
var select_el = document.querySelector('#create_certificate_form select[name=product_id]');

bt_minus.addEventListener('click', function () {
    if(quantity_el.value >= 2) {
        quantity_el.value = Number(quantity_el.value) - 1;
    };
    setValueIntoTotalPrice();
});

bt_plus.addEventListener('click', function () {
    quantity_el.value = Number(quantity_el.value) + 1;
    setValueIntoTotalPrice();
});

quantity_el.addEventListener('change', function () {
    quantity_el.value = checkInputValueForm(quantity_el.value);
    setValueIntoTotalPrice();
});

select_el.addEventListener('change', function () {
    setValueIntoTotalPrice();
});

function checkInputValueForm(value) {

    let input_val = Number(value);

    if(typeof(input_val) == 'number' && !isNaN(input_val)) {
        input_val = Math.round(input_val);
    } else {
        input_val = 1;
    };

    return input_val;
}

function setValueIntoTotalPrice() {

    let count = quantity_el.value;
    let active_product_id = select_el.value;
    let data_price_teg = 'price' + active_product_id;
    let price = document.querySelector('#create_certificate_form option[data-price' + active_product_id + ']').dataset[data_price_teg];
    let total_price = Number(count) * Number(price);
    document.querySelector('.total_price').innerHTML = String(total_price) + ' &#8364;';
}

/*
AJAX
it is required div with class='alert-div' in DOM
 */
class AjaxRequest {

    constructor(url = '/', data_post = {}, message_success = 'success Request', message_error = 'error') {
        this.url = url;
        this.data_post = this.parsDataPost(data_post);
        this.massage_success = message_success;
        this.message_error = message_error;
        this.self = this;
    }

    parsDataPost(data_post) {

        let result_text = '';

        for(let key in data_post) {
            result_text = result_text + key + '=' + data_post[key] + '&';
        }
        result_text = result_text.replace('/.$/', '');

        return result_text;
    }

    makeAjaxRequest() {

        this.httpRequest = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            this.httpRequest = new XMLHttpRequest();
            if (this.httpRequest.overrideMimeType) {
                this.httpRequest.overrideMimeType('text/xml');
            }
        } else if (window.ActiveXObject) { // IE
            try {
                this.httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    this.httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!this.httpRequest) {
            console.log('Невозможно создать экземпляр класса XMLHTTP ');
            return false;
        }
        this.httpRequest.onreadystatechange = this.checkStatusAjax.bind(this.self);
        this.httpRequest.open('POST', this.url, true);
        this.httpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        this.httpRequest.send(this.data_post);

    }

    checkStatusAjax() {

        if (this.httpRequest.readyState == 4) {
            if (this.httpRequest.status == 200 || this.httpRequest.status == 201) {
                this.createAlertMassage(this.httpRequest.status);
            } else {
                this.createAlertMassage();
            }
        }

    }

    createAlertMassage(status = false) {

        let dom_el = document.createElement('div');
        let alert_div = document.querySelector('.alert-div');

        if(status) {
            dom_el.className = 'alert alert-success mas-ajax-div';

            if(this.httpRequest.responseText && (this.httpRequest.responseText.length > 1)) {
                this.massage_success = this.httpRequest.responseText;
            }
            dom_el.innerHTML = this.massage_success;
        } else {

            if(this.httpRequest.responseText && (this.httpRequest.responseText.length > 1)) {
                this.message_error = this.httpRequest.responseText;
            }
            dom_el.className = 'alert alert-danger mas-ajax-div';
            dom_el.innerHTML = this.message_error;
        }
        alert_div.appendChild(dom_el);
        setTimeout(function () {
            alert_div.firstChild.remove();
        }, 3500);
    }
}
//end AJAX

document.querySelector('#create_certificate_form').addEventListener('submit', function (event) {
    event.preventDefault();
    let data_post = {};
    data_post._token = document.querySelector('#create_certificate_form input[name=_token]').value;
    data_post.name = document.querySelector('#create_certificate_form input[name=name]').value;
    data_post.last_name = document.querySelector('#create_certificate_form input[name=last_name]').value;
    data_post.email = document.querySelector('#create_certificate_form input[name=email]').value;
    data_post.product_id = document.querySelector('#create_certificate_form select[name=product_id]').value;
    data_post.number_of_trees = document.querySelector('#create_certificate_form input[name=number_of_trees]').value;
    let ajax_request = new AjaxRequest('certificates', data_post, 'The certificate has been created! You will receive an email', 'Sorry. Something is wrong');
    ajax_request.makeAjaxRequest();
}, true);

document.querySelector('#active_certificate_form').addEventListener('submit', function (event) {
    event.preventDefault();
    let data_post = {};
    data_post._token = document.querySelector('#active_certificate_form input[name=_token]').value;
    data_post.identity = document.querySelector('#active_certificate_form input[name=identity]').value;
    let ajax_request = new AjaxRequest('certificates/edit', data_post);
    ajax_request.makeAjaxRequest();
}, true);



//
//
