'use strict';
var tableon_main_table = null;
var tableon_columns_table = null;
var tableon_meta_table = null;
var tableon_vocabulary_table = null;
var tableon_predefinition_table = null;
//***



//***
//Popup with information about all shortcode possibilities
document.addEventListener('table23-html-drawn', function (e) {

    if (e.detail.otable.table_html_id === 'tableon-admin-table') {
        /*         
         e.detail.otable.table.querySelectorAll("th[data-key='shortcode']").forEach(function (item) {
         item.addEventListener('click', function (e) {
         let answer = new Object();
         document.dispatchEvent(new CustomEvent('table23-get', {detail: {
         table_html_id: item.closest('div.tableon-data-table').id,
         answer: answer
         }}));
         
         new Popup23({title: tableon_helper_vars.lang.shortcodes_help, action: 'tableon_get_smth', what: 'shortcodes_help'});
         
         }, false);
         });
         */
    }

    return true;
});

//***
//different backend popups data inits
document.addEventListener('tableon-popup-smth-loaded', e => {
    if (e.detail.what) {
        let what = e.detail.what;

        if (typeof what === 'string') {
            try {
                what = JSON.parse(what);
            } catch (e) {
                console.log(e);
            }
        }


        if (typeof what === 'object') {
            if (typeof what.call_action !== 'undefined') {
                switch (what.call_action) {
                    case 'tableon_show_column_field_option':
                        let container = e.detail.popup.node.querySelector('.tableon-table-json-data');
                        new TABLEON_ColumnsFieldsOptions(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
                        break;

                }
            }
        } else {
            e.detail.popup.set_content(e.detail.content);
        }
    }

});

document.addEventListener('tableon-tabs-switch', e => {
    //fix when in one popup some tables
    Array.from(document.querySelectorAll('.table23-flow-header')).forEach(function (item) {
        item.style.display = 'none';
    });

    //***
    let help_link = document.getElementById('main-table-help-link');
    switch (e.detail.current_tab_link.getAttribute('href')) {
         case '#tabs-columns':
            help_link.setAttribute('href', 'https://posts-table.com/document/columns/');
            break;
        case '#tabs-meta':
            help_link.setAttribute('href', 'https://posts-table.com/document/meta/');
            break;
        case '#tabs-filter':
            help_link.setAttribute('href', 'https://posts-table.com/document/posts-filter/');
            break;
        case '#tabs-predefinition':
            help_link.setAttribute('href', 'https://posts-table.com/document/predefinition/');
            break;
        case '#tabs-options':
            help_link.setAttribute('href', 'https://posts-table.com/document/options/');
            break;

        case '#tabs-custom-css':
            help_link.setAttribute('href', 'https://posts-table.com/document/custom-css/');

            //Custom CSS
            if (!tableon_main_table.custom_css_editor) {
                tableon_main_table.get_custom_css();
            }

            break;
    }
});

//overwriting CTRL+S behaviour for saving custom CSS
document.addEventListener('keydown', function (e) {
    if ((window.navigator.platform.match('Mac') ? e.metaKey : e.ctrlKey) && e.keyCode === 83) {
        if (tableon_main_table.custom_css_editor) {
            if (window.getComputedStyle(document.getElementById('tabs-custom-css'), null).getPropertyValue('display') === 'block') {
                tableon_main_table.save_custom_css();
                e.preventDefault();
            }
        }
    }
}, false);


window.onload = function () {

    new TABLEON_Tabs(document.querySelectorAll('.tableon-tabs'));

    //init data tables
    document.querySelectorAll('.tableon-table-json-data').forEach(function (container) {
        if (container.getAttribute('data-table-id') === 'tableon-admin-table') {
            tableon_main_table = new TABLEON_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
        } else {
            new TABLEON_GeneratedTables(JSON.parse(container.innerText), container.getAttribute('data-table-id'));
        }
    });

    //+++
    //settings
    new TABLEON_Settings(JSON.parse(document.querySelector('#tabs-main-settings .tableon-settings-json-data').innerText), 'tableon-settings-table');
    if (document.querySelector('.tableon-vocabulary-json-data')) {
        tableon_vocabulary_table = new TABLEON_GeneratedVocabulary(JSON.parse(document.querySelector('.tableon-vocabulary-json-data').innerText), 'tableon-vocabulary-table');
    }

    //***

    window.addEventListener('offline', function (e) {
        //tableon_helper.message(tableon_helper_vars.lang.offline, 'error', -1);
    });

    window.addEventListener('online', function (e) {
        tableon_helper.message(tableon_helper_vars.lang.online, 'notice');
    });

    if (tableon_helper_vars.mode === 'dev') {
        window.addEventListener('error', function (e) {
            tableon_helper.message(`Error: ${e.message}, ${e.filename}, #${e.lineno}`, 'error', -1);
        });
    }

};


//***

class TABLEON_Tabs {
    constructor(containers) {
        if (containers.length > 0) {
            for (let i = 0; i < containers.length; i++) {
                this.init(containers[i]);
            }
        }
    }

    init(container) {
        container.querySelectorAll('nav li a').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                a.parentElement.parentElement.querySelector('li.tab-current').removeAttribute('class');
                a.parentElement.className = 'tab-current';
                container.querySelector('.content-current').removeAttribute('class');
                container.querySelector('.content-wrap ' + a.getAttribute('href')).className = 'content-current';

                document.dispatchEvent(new CustomEvent('tableon-tabs-switch', {detail: {
                        current_tab_link: a
                    }}));

                return false;
            });
        });
    }
}


function tableon_change_thumbnail(button) {
    var post_id = button.closest('tr').getAttribute('data-pid');
    var field = 'thumbnail';

    var image = wp.media({
        title: tableon_helper_vars.lang.select_table_thumb,
        multiple: false,
        library: {
            type: ['image']
        }
    }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                uploaded_image = uploaded_image.toJSON();

                if (typeof uploaded_image.url != 'undefined') {
                    if (typeof uploaded_image.sizes.thumbnail !== 'undefined') {
                        button.querySelector('img').setAttribute('src', uploaded_image.sizes.thumbnail.url);
                    } else {
                        button.querySelector('img').setAttribute('src', uploaded_image.url);
                    }

                    tableon_helper.message(tableon_helper_vars.lang.saving, 'warning');

                    fetch(ajaxurl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: tableon_helper.prepare_ajax_form_data({
                            action: 'tableon_save_table_field',
                            post_id: post_id,
                            field: field,
                            value: uploaded_image.id
                        })
                    }).then(response => response.text()).then(data => {
                        tableon_helper.message(tableon_helper_vars.lang.saved, 'notice');
                    }).catch((err) => {
                        tableon_helper.message(err, 'error', 5000);
                    });

                }
            });


    return false;

}


function tableon_import_options() {

    if (document.getElementById('tableon-import-text').value) {
        let data = JSON.parse(document.getElementById('tableon-import-text').value);

        if (typeof data === 'object') {
            if (confirm(tableon_helper_vars.lang.sure)) {
                tableon_helper.message(tableon_helper_vars.lang.importing, 'warning');
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json'
                    },
                    credentials: 'same-origin',
                    body: tableon_helper.prepare_ajax_form_data({
                        action: 'tableon_import_data',
                        data: JSON.stringify(data)
                    })
                }).then(response => response.text()).then(data => {
                    tableon_helper.message(tableon_helper_vars.lang.imported, 'notice');
                    window.location.reload();
                }).catch((err) => {
                    tableon_helper.message(err, 'error', 5000);
                });
            }
        } else {
            tableon_helper.message(tableon_helper_vars.lang.error, 'error', 5000);
        }
    }
}