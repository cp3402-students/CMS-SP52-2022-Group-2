'use strict';

class TABLEON_FilterFieldsOptions extends TABLEON_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'tableon_save_filter_field_option';//reinit ajax action
        this.switcher_action = 'tableon_save_filter_field_option';
        this.init_switchers_listener();
    }

    do_after_draw() {
        super.do_after_draw();
        //as we can have different elements in field value elements actons should be inited here
        this.init_html_items_action('select', 'change');
        //this.init_html_items_action('input[type="text"]', 'change');
        this.init_html_items_action('input', 'change');
    }

    init_html_items_action(html_item_type, action) {
        this.table.querySelectorAll(`.table23_td_value ${html_item_type}.tableon-filter-field-option`).forEach(item => {
            item.addEventListener(action, e => {
                e.stopPropagation();

                let table_id = item.getAttribute('data-table-id');
                let key = item.getAttribute('data-key');

                this.message(tableon_helper_vars.lang.saving, 'warning');

                fetch(this.settings.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: this.prepare_ajax_form_data({
                        action: this.save_table_field_action,
                        table_id: table_id,
                        key: key,
                        value: item.value
                    })
                }).then(response => response.text()).then(data => {
                    this.message(tableon_helper_vars.lang.saved);
                }).catch((err) => {
                    this.message(tableon_helper_vars.lang.error + ' ' + err, 'error');
                });

                return true;
            });
        });
    }

}