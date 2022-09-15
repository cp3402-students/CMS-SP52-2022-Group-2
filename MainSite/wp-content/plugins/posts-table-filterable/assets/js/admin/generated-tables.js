'use strict';
class TABLEON_GeneratedTables extends DataTable23 {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);

        this.use_cache = false;

        this.save_table_field_action = 'tableon_save_table_field';//ajax action for saving
        this.delete_action = 'tableon_delete_table';//ajax action for deleting
        this.clone_action = 'tableon_clone_table';//ajax action for deleting
        this.switcher_action = 'tableon_save_table_field';
        this.custom_css_editor = null;

        //***

        let _this = this;

        this.wrapper.parentElement.querySelectorAll('.tableon-text-search').forEach(function (input) {
            input.addEventListener('keyup', function (e) {

                e.stopPropagation();

                let data_key = input.getAttribute('data-key');

                let add = {};
                let do_search = false;

                switch (e.keyCode) {
                    case 13:
                        add[data_key] = input.value;
                        do_search = true;
                        break;

                    case 27:
                        delete _this.request_data.filter_data[data_key];
                        do_search = true;
                        break;
                }

                if (do_search) {
                    _this.request_data.current_page = 0;
                    if (typeof _this.request_data.filter_data !== 'object' && _this.request_data.filter_data.length > 0) {
                        _this.request_data.filter_data = JSON.parse(_this.request_data.filter_data);
                    }
                    _this.request_data.filter_data = _this.extend(_this.request_data.filter_data, add);
                    _this.draw_data();
                }

            });

            //click on cross
            input.addEventListener('mouseup', function (e) {
                e.stopPropagation();
                if (input.value.length > 0) {
                    let data_key = input.getAttribute('data-key');
                    setTimeout(function () {
                        if (input.value.length === 0) {
                            delete _this.request_data.filter_data[data_key];
                            _this.request_data.current_page = 0;
                            _this.draw_data();
                        }
                    }, 5);
                }
            });

        });

        //for switchers actions casting
        this.init_switchers_listener();
        this.init_json_fields_saving();
        //save columns for filter
        this.init_filters_blocks_listener();
    }

    init_filters_blocks_listener() {
        if (this.constructor.name === 'TABLEON_GeneratedTables') {//do not init it for inherited classes

            if (typeof this.filters_blocks_listener_lock === 'undefined') {
                this.filters_blocks_listener_lock = true;
            }

            if (this.filters_blocks_listener_lock) {
                let _this = this;
                this.filters_blocks_listener_lock = false;
                document.addEventListener('block-constructor23-changed', function (e) {
                    e.stopPropagation();
                    if (e.detail.connect_id === 'tableon_tables_filter') {
                        _this.message(tableon_helper_vars.lang.saving + ' ...', 'warning');
                        fetch(_this.settings.ajax_url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            body: _this.prepare_ajax_form_data({
                                action: 'tableon_save_fields_for_filter',
                                donor_data: JSON.stringify(e.detail.donor_data),
                                acceptor_data: JSON.stringify(e.detail.acceptor_data),
                                post_id: e.detail.additional.table_id
                            })
                        }).then(response => response.text()).then(data => {
                            _this.message(tableon_helper_vars.lang.saved);
                        }).catch((err) => {
                            console.log(err);
                            _this.message(err, 'error', 5000);
                        });

                    }
                });
            }

        }
    }

    init_switchers_listener() {
        //With inheriting this js class custom events adds multiple times, so var flags is uses here to avoid it, and now in js no way to get all attached actions to the document
        if (tableon_helper_vars.flags.indexOf(this.switcher_action) === -1) {
            document.addEventListener(this.switcher_action, e => {
                //e.preventDefault();
                this.save(e.detail.post_id, e.detail.name, e.detail.value, null, '', e.detail.custom_data);
            });
        }
        tableon_helper_vars.flags.push(this.switcher_action);
    }

    do_after_draw() {
        super.do_after_draw();
        //fade out loader
        if (document.querySelector('.tableon-admin-preloader')) {
            document.querySelector('.tableon-admin-preloader').classList.add('hide-opacity');
            setTimeout(function () {
                document.querySelector('.tableon-admin-preloader').style.display = 'none';
            }, 777);
        }

        //***

        let _this = this;
        _this.table.querySelectorAll('.table23-td-editable').forEach(function (td) {
            let type = td.getAttribute('data-field-type');
            let field = td.getAttribute('data-field');
            let post_id = td.getAttribute('data-pid');

            //fix for tables as options, where on different rows needs different type of editing
            if (type === 'textinput') {
                if (td.querySelectorAll('select').length > 0) {
                    type = 'select';
                    td.setAttribute('data-field-type', type);
                }

                if (td.querySelectorAll('input[type="checkbox"]').length > 0) {
                    type = 'checkbox';
                    td.setAttribute('data-field-type', type);
                }
            }

            switch (type) {
                case 'textinput':

                    td.addEventListener('click', function (e) {
                        e.stopPropagation();
                        if (!td.querySelector('textarea')) {
                            let input = document.createElement('textarea');
                            //input.setAttribute('type', 'text');
                            input.className = 'table23-editable-textarea';

                            let prev_value = input.value = td.innerHTML;
                            td.innerHTML = '';

                            input.addEventListener('keydown', function (e) {

                                e.stopPropagation();

                                if (e.keyCode === 13) {
                                    e.preventDefault();

                                    td.innerHTML = input.value.trim();

                                    if (input.value !== prev_value) {
                                        _this.save(post_id, field, input.value);
                                    }

                                    //return false;
                                }

                                if (e.keyCode === 27) {//escape
                                    td.innerHTML = prev_value;
                                }

                            });

                            td.appendChild(input);
                            input.focus();
                        }

                        return true;
                    });

                    break;

                case 'select':
                    let select = td.querySelector('select');

                    select.addEventListener('change', function (e) {
                        e.stopPropagation();

                        let values = Array.from(this.querySelectorAll('option:checked')).map(elem => elem.value).join(',');

                        _this.save(post_id, field, values, this.getAttribute('data-action'), this.getAttribute('data-additional'));
                        return true;
                    });


                    if (typeof SelectM23 === 'function') {
                        new SelectM23(select, true);//wrapping of <select>

                        select.addEventListener('selectm23-reorder', function (e) {
                            _this.save(post_id, field, e.detail.values, this.getAttribute('data-action'), this.getAttribute('data-additional'));
                        });
                    }

                    break;
            }

        });

        //init switchers
        Array.from(this.table.querySelectorAll('.switcher23')).forEach((button) => {
            tableon_helper.init_switcher(button);
        });


        document.dispatchEvent(new CustomEvent('table23-do-after-draw', {detail: {
                table_html_id: this.table_html_id
            }}));

    }

    //**********************************************************************************************

    save(post_id, field, value, ajax_action = null, additional = '', custom_data = null) {
        this.message(tableon_helper_vars.lang.saving + ' ...', 'warning');
        let action = this.save_table_field_action;

        if (ajax_action) {
            action = ajax_action;
        }

        let form_data = {
            action: action,
            field: field,
            post_id: post_id,
            value: value,
            additional: additional
        };

        if (custom_data) {
            form_data = {...form_data, ...custom_data};
        }

        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin', // 'include', default: 'omit'
            body: this.prepare_ajax_form_data(form_data)
        }).then(response => response.json()).then(data => {
            this.message(tableon_helper_vars.lang.saved);

            document.dispatchEvent(new CustomEvent('after_' + this.save_table_field_action, {detail: {
                    self: this,
                    post_id: post_id,
                    field: field,
                    value: value
                }}));

        }).catch((err) => {
            console.log(err);
            this.message(tableon_helper_vars.lang.error + ' ' + err, 'error');
        });
    }

    /***************************************/

    create() {
        this.message(tableon_helper_vars.lang.creating + ' ...', 'warning');

        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin', // 'include', default: 'omit'
            body: this.prepare_ajax_form_data({
                action: 'tableon_create_table'
            })
        }).then(response => response.json()).then(table_data => {
            this.message(tableon_helper_vars.lang.created);
            this.request_data.orderby = 'id';
            this.request_data.order = 'desc';
            this.settings.table_data = table_data;
            this.draw_data(null);
        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });
    }

    delete(id) {
        if (confirm(tableon_helper_vars.lang.sure)) {
            this.message(tableon_helper_vars.lang.deleting + ' ...', 'warning');
            this.delete_row(id);
            fetch(this.settings.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: this.prepare_ajax_form_data({
                    action: this.delete_action,
                    id: id
                })
            }).then(response => response.json()).then(data => {
                this.message(tableon_helper_vars.lang.deleted);
            }).catch((err) => {
                console.log(err);
                this.message(err, 'error', 5000);
            });
        }
    }

    clone(id) {
        if (confirm(tableon_helper_vars.lang.sure)) {
            this.message(tableon_helper_vars.lang.cloning + ' ...', 'warning');
            fetch(this.settings.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: this.prepare_ajax_form_data({
                    action: this.clone_action,
                    id: id
                })
            }).then(response => response.json()).then(table_data => {
                this.message(tableon_helper_vars.lang.cloned);
                this.request_data.orderby = 'id';
                this.request_data.order = 'desc';
                this.settings.table_data = table_data;
                this.draw_data(null);
            }).catch((err) => {
                console.log(err);
                this.message(err, 'error', 5000);
            });
        }
    }

    //popup of table options
    call_popup(post_id) {
        this.table_id = post_id;
        document.getElementById('tableon-popup-columns-template').style.display = 'block';

        if (document.getElementById('tableon-popup-columns-template').querySelector('.tableon-columns-table-zone table')) {
            document.getElementById('tableon-popup-columns-template').querySelector('.tableon-columns-table-zone table').remove();
        }

        document.getElementById('tableon-popup-columns-template').querySelector('.tableon-columns-table-zone').innerHTML = tableon_helper.get_loader_html();
        document.getElementById('tableon-popup-columns-template').querySelector('.tableon-table-options-zone').innerHTML = tableon_helper.get_loader_html();
        document.getElementById('tableon-popup-columns-template').querySelector('.tableon-meta-table-zone').innerHTML = tableon_helper.get_loader_html();
        document.getElementById('tableon-popup-columns-template').querySelector('.tableon-modal-title').innerHTML = '#' + post_id + '. ' + this.table.querySelector('.table23_td_title[data-pid="' + post_id + '"]').innerHTML;

        this.custom_css_editor = null;
        document.querySelector('.tableon-options-custom-css-zone').innerHTML = '';
        if (window.getComputedStyle(document.getElementById('tabs-custom-css'), null).getPropertyValue('display') === 'block') {
            this.get_custom_css();
        }

        //get_columns_data   
        let table_columns_html_id = tableon_helper.create_id('t');
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin', // 'include', default: 'omit'
            body: this.prepare_ajax_form_data({
                action: 'tableon_get_columns_data',
                post_id: post_id,
                table_html_id: table_columns_html_id
            })
        }).then(response => response.text()).then(html => {
            document.getElementById('tableon-popup-columns-template').querySelector('.tableon-columns-table-zone').innerHTML = html;

            if (tableon_columns_table) {
                tableon_columns_table.destructor();//detach tableon_save_table_field
            }

            tableon_columns_table = new TABLEON_GeneratedColumns(JSON.parse(document.querySelector(`[data-table-id="${table_columns_html_id}"]`).innerText), table_columns_html_id);
        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });


        //get_columns_options_data
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'tableon_get_tables_options',
                post_id: post_id
            })
        }).then(response => response.text()).then(data => {
            document.getElementById('tableon-popup-columns-template').querySelector('.tableon-table-options-zone').innerHTML = '<div class="data-table-23 tableon-data-table data-table-23-separated" id="tableon-table-options"><table></table></div>';

            let d = JSON.parse(data);
            d.request_data.post_id = post_id;
            new TABLEON_TablesOptions(d, 'tableon-table-options');
        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });


        //get_columns_meta_data
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'tableon_get_tables_meta',
                post_id: post_id
            })
        }).then(response => response.text()).then(data => {
            document.getElementById('tableon-popup-columns-template').querySelector('.tableon-meta-table-zone').innerHTML = '<div class="data-table-23 tableon-data-table data-table-23-separated" id="tableon-meta-columns-table"><table></table></div>';

            let d = JSON.parse(data);
            d.request_data.post_id = post_id;
            tableon_meta_table = new TABLEON_MetaTable(d, 'tableon-meta-columns-table');
        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });

        //***


        //get relevant fields for filter
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'tableon_get_fields_for_filter',
                post_id: post_id
            })
        }).then(response => response.text()).then(data => {
            document.getElementById('tableon-popup-columns-template').querySelector('#tabs-filter .tabs-filter-container').innerHTML = '<div class="blocks-constructor-23 filter-fields"></div>';

            document.getElementById('tableon-popup-columns-template').querySelector('#tabs-filter .tabs-filter-container .filter-fields').innerHTML = '';
            let d = JSON.parse(data);

            if (Object.keys(d.donor_data).length === 0 && Object.keys(d.acceptor_data).length === 0) {
                document.getElementById('tableon-popup-columns-template').querySelector('#tabs-filter .tabs-filter-container .filter-fields').innerHTML = tableon_helper_vars.lang.no_data;
            } else {
                new BlockConstructor23(document.querySelector('.filter-fields'), 'tableon_tables_filter', d.donor_data, d.acceptor_data, {table_id: post_id});
            }


        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });

        //***

        //get predefinition data table        
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'tableon_get_predefinition_table',
                post_id: post_id
            })
        }).then(response => response.text()).then(html => {
            document.getElementById('tabs-predefinition').querySelector('.tableon-predefinition-table-zone').innerHTML = html;
            tableon_predefinition_table = new TABLEON_Predefinition(JSON.parse(document.querySelector('[data-table-id="tableon-predefinition-table"]').innerText), 'tableon-predefinition-table');
        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });

    }

    save_custom_css() {
        this.message(tableon_helper_vars.lang.saving + ' ...', 'warning');
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'tableon_save_table_custom_css',
                table_id: this.table_id,
                value: this.custom_css_editor.codemirror.getValue()
            })
        }).then(response => response.text()).then(data => {
            this.message(tableon_helper_vars.lang.saved);
        }).catch((err) => {
            console.log(err);
            this.message(err, 'error', 5000);
        });
    }

    get_custom_css() {
        if (this.custom_css_editor !== 1) {
            this.custom_css_editor = 1;//flag to avoid double requesting
            let zone = document.querySelector('.tableon-options-custom-css-zone');
            zone.innerHTML = tableon_helper.get_loader_html();

            fetch(this.settings.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: this.prepare_ajax_form_data({
                    action: 'tableon_get_table_custom_css',
                    table_id: this.table_id
                })
            }).then(response => response.text()).then(data => {

                zone.innerHTML = '';
                let custom_css_textarea = document.createElement('textarea');
                custom_css_textarea.setAttribute('id', 'table-custom-css-textarea');
                custom_css_textarea.value = data;
                zone.appendChild(custom_css_textarea);
                this.custom_css_editor = wp.codeEditor.initialize(custom_css_textarea, {
                    type: 'css',
                    lineNumbers: true,
                    indentUnit: 2,
                    tabSize: 2
                });

            }).catch((err) => {
                console.log(err);
                this.message(err, 'error', 5000);
            });
        }
    }

    message(text, type = 'notice', duration = 0) {
        tableon_helper.message(text, type, duration);
    }

    //for popup with a table settings
    add_scroll_action(node) {
        let elem = node.querySelector('.tableon-data-table > .table23-wrapper');
        if (elem) {
            let flow = elem.querySelector('.table23-flow-header');

            if (flow) {
                let box = elem.getBoundingClientRect();
                let box2 = document.getElementById('tableon-popup-columns-template').querySelector('.tableon-modal-inner-header').getBoundingClientRect();
                let first_row = elem.querySelector('table thead tr');


                if (box.top <= 5) {

                    flow.style.display = 'block';
                    flow.style.width = (elem.querySelector('table').offsetWidth + 10) + 'px';
                    flow.style.top = 2 * Math.abs(box2.height) + Math.abs(box.top) + 'px';

                    Array.from(first_row.querySelectorAll('th')).forEach((th, index) => {
                        flow.querySelectorAll('div')[index].style.width = th.offsetWidth + 1 + 'px';
                        flow.querySelectorAll('div')[index].innerHTML = th.innerText;
                    });

                } else {
                    flow.style.display = 'none';
                }
            }
        }
    }

}

