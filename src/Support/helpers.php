<?php

if (!function_exists('flush_form_schema_cache')) {

    /**
     * Flushes the content type cache
     *
     * @param int $id
     */
    function flush_form_schema_cache($id)
    {
    	\Cache::forget('form.' . $id);
    	\Cache::forget('form.' . $id . '.rules');
    }

}