<?php

class WP_JSON_Authentication_OAuth1_ListTable extends WP_List_Table {
	public function prepare_items() {
		$paged = $this->get_pagenum();

		$args = array(
			'post_type'   => 'json_consumer',
			'post_status' => 'any',
			'meta_query'  => array(
				array(
					'key'   => 'type',
					'value' => 'oauth1',
				),
			),

			'paged' => $paged,
		);

		$query       = new WP_Query();
		$this->items = $query->query( $args );
	}

	/**
	 * Get a list of columns for the list table.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 * @return array Array in which the key is the ID of the column,
	 *               and the value is the description.
	 */
	public function get_columns() {
		$c = array(
			'cb'              => '<input type="checkbox" />',
			'name'            => __( 'Name' ),
			'type'            => __( 'Type' ),
			'description'     => __( 'Description' ),
			'consumer_key'    => __( 'Consumer Key', 'json_oauth' ),
			'consumer_secret' => __( 'Consumer Secret', 'json_oauth' ),
		);

		return $c;
	}

	public function column_cb( $item ) {
		?>
		<label class="screen-reader-text"
		       for="cb-select-<?php echo $item->ID ?>"><?php _e( 'Select consumer', 'json_oauth' ); ?></label>
		<input id="cb-select-<?php echo $item->ID ?>" type="checkbox" name="consumers[]"
		       value="<?php echo $item->ID ?>"/>
		<?php
	}

	protected function column_name( $item ) {
		$title = get_the_title( $item->ID );
		if ( empty( $title ) ) {
			$title = '<em>' . __( 'Untitled' ) . '</em>';
		}

		$edit_link = add_query_arg(
			array(
				'action' => 'json-oauth-edit',
				'id'     => $item->ID,
			),
			admin_url( 'admin.php' )
		);

		$actions     = array(
			'edit' => sprintf( '<a href="%s">%s</a>', $edit_link, __( 'Edit' ) ),
		);
		$action_html = $this->row_actions( $actions );

		return $title . ' ' . $action_html;
	}

	protected function column_description( $item ) {
		return $item->post_content;
	}

	protected function column_type( $item ) {
		printf( '<strong>%s</strong>', esc_html( get_post_meta( $item->ID, 'type', true ) ) );
	}

	protected function column_consumer_secret( $item ) {
		printf( '<input type="text" readonly value="%s" onclick="this.select();" />', esc_attr( get_post_meta( $item->ID, 'secret', true ) ) );
	}

	protected function column_consumer_key( $item ) {
		printf( '<input type="text" readonly value="%s" onclick="this.select();" />', esc_attr( get_post_meta( $item->ID, 'key', true ) ) );
	}
}
