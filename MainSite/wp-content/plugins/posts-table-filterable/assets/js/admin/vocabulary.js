'use strict';
class TABLEON_GeneratedVocabulary extends TABLEON_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'tableon_save_vocabulary_field';//reinit ajax action
        this.switcher_action = 'tableon_save_vocabulary_field';
        this.init_switchers_listener();
        this.init_json_fields_saving();
    }

    do_after_draw() {
        super.do_after_draw();
    }

    create() {
        this.message(tableon_helper_vars.lang.creating, 'warning');
        fetch(this.settings.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: this.prepare_ajax_form_data({
                action: 'tableon_create_vocabulary_field',
                tail: tableon_helper.create_id('a')
            })
        }).then(response => response.json()).then(data => {
            this.message(tableon_helper_vars.lang.created);
            tableon_vocabulary_table.settings.table_data = data;
            tableon_vocabulary_table.draw_data(null);
        }).catch((err) => {
            this.message(tableon_helper_vars.lang.error + ' ' + err, 'error');
        });
    }

    delete(id) {
        if (confirm(tableon_helper_vars.lang.sure)) {
            this.message(tableon_helper_vars.lang.deleting, 'warning');
            tableon_vocabulary_table.delete_row(id);
            fetch(this.settings.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: this.prepare_ajax_form_data({
                    action: 'tableon_delete_vocabulary_field',
                    id: id
                })
            }).then(response => response.json()).then(data => {
                this.message(tableon_helper_vars.lang.deleted);
                tableon_vocabulary_table.settings.table_data = data;
            }).catch((err) => {
                this.message(tableon_helper_vars.lang.error + ' ' + err, 'error');
            });
        }
    }
}
