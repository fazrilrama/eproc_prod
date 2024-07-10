<?php
class AsyncTask
{
    public function emailer($emails = array(), $params = array())
    {
        $url = str_replace('.gkpd', '', site_url('async_task/send_email'));
        $curls = [];
        $async = true;
        if (isset($params['url'])) $url = $params['url'];
        if (isset($params['async'])) $async = $params['async'];

        for ($i = 0; $i < count($emails); $i++) {
            $e = $emails[$i];
            $from_name = $e['from_name'];
            $to = $e['to'];
            $subject = $e['subject'];
            $message = $e['message'];

            $curls[$i] = curl_init();
            curl_setopt($curls[$i], CURLOPT_URL, $url);
            curl_setopt($curls[$i], CURLOPT_POST, true);
            curl_setopt($curls[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(
                $curls[$i],
                CURLOPT_POSTFIELDS,
                "from_name=$from_name&to=$to&subject=$subject&message=$message"
            );
        }

        return $this->cURLProcessor($curls, $async);
    }

    public function cURLProcessor($curls = [], $async = true)
    {
        $result = array();

        if (count($curls) > 0) {

            for ($i = 0; $i < count($curls); $i++) {
                if ($async) {
                    curl_setopt($curls[$i], CURLOPT_TIMEOUT, 1);
                    curl_setopt($curls[$i], CURLOPT_NOSIGNAL, 1);
                }
            }

            $process = curl_multi_init();
            for ($i = 0; $i < count($curls); $i++) {
                curl_multi_add_handle($process, $curls[$i]);
            }

            $active = null;
            do {
                $status = curl_multi_exec($process, $active);
                if ($active) {
                    // Wait a short time for more activity
                    curl_multi_select($process);
                }
            } while ($active && $status == CURLM_OK);

            for ($i = 0; $i < count($curls); $i++) {
                $result[$i] = curl_multi_getcontent($curls[$i]);
                $result[$i] = array(
                    'success' => true, 'is_async' => $async, 'data' => $result[$i], 'url' => curl_getinfo($curls[$i])['url']
                );
                curl_multi_remove_handle($process, $curls[$i]);
            }
            curl_multi_close($process);
        }

        return $result;
    }
}
