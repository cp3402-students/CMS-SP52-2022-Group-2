'use strict';
class TABLEON_MetaTable extends TABLEON_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'tableon_save_table_meta_field';//reinit ajax action
        this.delete_action = 'tableon_delete_table_meta';//ajax action for deleting

        this.init_json_fields_saving();

        document.getElementById('tableon-popup-columns-template').querySelector('.tableon-modal-inner-content').addEventListener('scroll', (e) => {
            this.add_scroll_action(document.getElementById('tabs-meta').querySelector('.tableon-meta-table-zone'));
        });
    }

    init_json_fields_saving() {
        super.init_json_fields_saving();

        document.addEventListener('after_' + this.save_table_field_action, function (e) {
            e.stopPropagation();

            if (e.detail.field === 'title' || e.detail.field === 'meta_key') {
                tableon_columns_table.refresh();
            }
        });

    }

    do_after_draw() {
        super.do_after_draw();
    }

    create() {
        this.message(tableon_helper_vars.lang.creating + ' ...', 'warning');

        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin', // 'include', default: 'omit'                   
            body: this.prepare_ajax_form_data({
                action: 'tableon_create_meta',
                table_id: this.request_data.post_id
            })
        }).then(response => response.json()).then(data => {
            this.message(tableon_helper_vars.lang.created);
            this.settings.table_data = data;
            this.draw_data(null);
        }).catch((err) => {
            this.message(err, 'error', 5000);
        });
    }

}