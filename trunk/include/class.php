<?php

	if( !function_exists( 'hw_cyrlat_filename' ) ){
		/**
		 * @return hw_cyrlat_filename
		 */
		function hw_cyrlat_filename(){
			static $class;
			if( !$class instanceof hw_cyrlat_filename )
				$class = new hw_cyrlat_filename();
			return $class;
		}


		class hw_cyrlat_filename{

			public function convert( $source, $strtolower = true, $convert_slug = false ){

				$iso9_table = array(
					'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G', 'Ґ' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE', 'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'J', 'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K', 'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS', 'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '',
					'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g', 'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye', 'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'j', 'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k', 'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
					'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', ' ' => '-'
				);
				$geo2lat = array(
					'ა' => 'a', 'ბ' => 'b', 'გ' => 'g', 'დ' => 'd', 'ე' => 'e', 'ვ' => 'v', 'ზ' => 'z', 'თ' => 'th', 'ი' => 'i', 'კ' => 'k', 'ლ' => 'l', 'მ' => 'm', 'ნ' => 'n', 'ო' => 'o', 'პ' => 'p', 'ჟ' => 'zh', 'რ' => 'r', 'ს' => 's', 'ტ' => 't', 'უ' => 'u', 'ფ' => 'ph', 'ქ' => 'q', 'ღ' => 'gh', 'ყ' => 'qh', 'შ' => 'sh', 'ჩ' => 'ch', 'ც' => 'ts', 'ძ' => 'dz', 'წ' => 'ts', 'ჭ' => 'tch', 'ხ' => 'kh', 'ჯ' => 'j', 'ჰ' => 'h'
				);
				$iso9_table = array_merge( $iso9_table, $geo2lat );

				$locale = get_locale();
				switch( $locale ){
					case 'bg_BG':
						$iso9_table['Щ'] = 'SHT';
						$iso9_table['щ'] = 'sht';
						$iso9_table['Ъ'] = 'A';
						$iso9_table['ъ'] = 'a';
						break;
					case 'uk':
					case 'uk_ua':
					case 'uk_UA':
						$iso9_table['И'] = 'Y';
						$iso9_table['и'] = 'y';
						break;
				}
				$source = trim( strtr( trim( $source ), $iso9_table ) );
				///
				if( function_exists( 'iconv' ) ){
					$source = iconv( 'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $source );
				}
				///
				if( $convert_slug ){
					$source = preg_replace( "/[^A-Za-z0-9'_\-\.]/", '-', $source );
					$source = preg_replace( '/\-+/', '-', $source );
					$source = preg_replace( '/^-+/', '', $source );
					$source = preg_replace( '/-+$/', '', $source );
					$source = str_replace( '.', '-', $source );
				}

				return $strtolower ? strtolower( $source ) : $source;
			}


			public function rename_exists_file( $attached_id_orPost ){
				if( is_numeric( $attached_id_orPost ) ){
					$attached_id_orPost = get_post( $attached_id_orPost );
				}
				if( !$attached_id_orPost instanceof WP_Post ){
					return false;
				}
				///
				$uploadDir = wp_upload_dir();
				$uploadDir_dir = $uploadDir['basedir'];
				$uploadDir_url = $uploadDir['baseurl'];
				$metadata = wp_get_attachment_metadata( $attached_id_orPost->ID );
				$name_expode = explode( '/', $metadata['file'] );
				$basename = end( $name_expode );
				$basename_converted = $this->convert( $basename );
				$basename_converted_slug = $this->convert( $basename, true, true );
				$workpath = $uploadDir_dir . '/' . dirname( $metadata['file'] );
				$workurl = $uploadDir_url . '/' . dirname( $metadata['file'] );
				///
				if( $basename != $basename_converted ){
					wp_update_post( [ 'ID' => $attached_id_orPost->ID, 'guid' => $workurl . '/' . $basename_converted, 'post_name' => $basename_converted_slug ] );
					@rename( $workpath . '/' . $basename, $workpath . '/' . $basename_converted );
					////
					$metadata['file'] = $basename_converted;
					if( isset( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) )
						foreach( $metadata['sizes'] as $size_name => $size ){
							$new_name = $this->convert( $size['file'] );
							$new_path = $workpath . '/' . $new_name;
							$old_path = $workpath . '/' . $size['file'];
							@rename( $old_path, $new_path );
							$metadata['sizes'][ $size_name ]['file'] = $new_name;
						}
					return update_post_meta( $attached_id_orPost->ID, '_wp_attachment_metadata', $metadata, false );
				}
				return false;
			}


			public function scan_and_convert_fiels(){
				$medias = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'any', 'posts_per_page' => - 1 ) );
				foreach( $medias as $media ){
					$this->rename_exists_file( $media );
				}
			}

		}
	}

