'use strict';
class TABLEON_Settings extends TABLEON_GeneratedTables {
    constructor(table_data, table_html_id) {
        super(table_data, table_html_id);
        this.save_table_field_action = 'tableon_save_settings_field';//reinit ajax action
        this.switcher_action = 'tableon_save_settings_field';
        this.init_switchers_listener();

    }

    do_after_draw() {
        super.do_after_draw();
    }
}
