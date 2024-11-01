<?php

defined('ABSPATH') || exit;

add_filter('woocommerce_rest_prepare_product_object', 'smartcustomizer_product_response', 20, 3);
add_filter('woocommerce_rest_prepare_product_variation_object', 'smartcustomizer_product_response', 20, 3);

function smartcustomizer_product_response($response, $object, $request)
{
    $variations = $response->data['variations'];
    $variations_res = array();
    $variations_array = array();
    if (!empty($variations) && is_array($variations)) {

        $query_images_args = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
        );

        $query_images = new WP_Query($query_images_args);

        $images = array();
        foreach ($query_images->posts as $image) {
            $images[$image->ID] = wp_get_attachment_url($image->ID);
        }


        foreach ($variations as $variation) {
            $variation_id = $variation;
            $variation = new WC_Product_Variation($variation_id);

            $variations_res['id'] = $variation_id;

            $variations_res['image'] = $variation->image_id && !empty($images[$variation->image_id]) ? $images[$variation->image_id] : '';

            $attributes = [];
            // variation attributes
            foreach ($variation->get_variation_attributes() as $attribute_name => $attribute) {
                $attributes[] = wc_attribute_label(str_replace('attribute_', '', $attribute_name), $variation) . ' ' . $attribute;
                /* 'slug'   => str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $attribute_name ) ), */
            }

            $variations_res['title'] = implode(' / ', $attributes);
            $variations_array[] = $variations_res;
        }
    }
    $response->data['product_variations'] = $variations_array;

    $response->data['image'] = $response->data['images'] ? $response->data['images'][0]->src : '';

    return $response;
}


function smartcustomizer_get_app_url()
{
    if (SMARTCUSTOMIZER_MODE === 'dev') {
        return str_replace('https', 'http', SMARTCUSTOMIZER_URL);
    }
    return SMARTCUSTOMIZER_URL;
}


function smartcustomizer_get_link($design_id, $link_suffix = 'zip')
{
    return smartcustomizer_get_app_url() . 'design/' . $design_id . '/' . $link_suffix;
}

function smartcustomizer_get_image($design_id, $product_image = null, $return_link = false)
{

    $image_link = smartcustomizer_get_app_url() . 'design/' . $design_id . '/preview.jpg';

    if ($return_link) {
        return $image_link;
    }

    if (empty($product_image)) {
        return '<img src="' . esc_url($image_link) . '" width="150" height="150">';
    }

    $doc = new DOMDocument();
    $doc->loadHTML($product_image);
    $image = $doc->getElementsByTagName('img')->item(0);

    $tags = ['src', 'srcset', 'data-src', 'data-srcset'];

    // $src = esc_url($src);

    foreach ($tags as $tag) {
        if ($image->getAttribute($tag)) {
            $image->setAttribute($tag, $image_link);
        }
    }


    return $doc->saveHTML();
}
