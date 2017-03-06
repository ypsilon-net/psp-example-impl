<!DOCTYPE html>
    <head>
        <!-- php https-request to getting library path, authoken and status -->
        <?php
            $url          = base_url().'/payframe/whitepage';
            $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            $length       = 10;

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $today   = date("Ymd");
            $rand    = strtoupper(substr(uniqid(sha1(time())),0,4));
            $orderid = $today . $rand;

            $fields = array(
                // set the test-username from the docu here
                'username'    => 'username',
                // set the test-password from the docu here
                'password'    => 'password',
                'amount'      => '10,76',
                'currency'    => 'EUR',
                'orderid'     => $orderid,
                'urlresult'   => 'https://httpbin.org/post',
                'secret'      => $randomString,
                'language'    => 'de',
                'description' => 'booking['.$orderid.']',
            );

            $params = http_build_query($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            // display errors if something goes wrong
            if($result === false) {
                echo "<pre>"; print_r(curl_error($ch)); echo "</pre>";
            }

            curl_close($ch);
            $payAnswer = json_decode($result, true);
            if($payAnswer['success'] === FALSE) {
                echo "<pre>"; print_r($payAnswer['errors']); echo "</pre>";
            } else {
                $jsurl     = $payAnswer['jsurl'];
                $authtoken = $payAnswer['authtoken'];
            }

            // to implement the library as a file
            // $jScript = file_get_contents($payAnswer['jsurl']);
        ?>

        <title>Customer Whitepage Example</title>

        <!-- library implementation with src -->
        <script type="text/javascript"
                charset="utf8" src="<?php echo $jsurl; ?>">
        </script>

        <!-- library implementation as file -->
        <!-- <script><?php // echo $jScript; ?></script> -->

        <!-- jQuery implemntation ## optional -->
        <script type="te"
                src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js">
        </script>

        <style>
            .wp-container {
                width:                      600px;
            }
            .wp-container h2 {
                margin-left:                9px;
            }
            .wp-container input[type="submit"] {
                border-radius:              5px;
                float:                      right;
                margin-right:               8px;
                padding:                    10px;
                width:                      220px;
            }
            .wp-customer-disable-submit-container {
                background:     #fff none repeat scroll 0 0;
                border:         1px solid #dbdbdb;
                border-radius:  5px;
                float:          left;
                margin-left:    8px;
                padding:        10px;
            }
            textarea {
                border:                     1px solid #ddd;
                border-radius:              10px;
                height:                     190px;
                padding:                    8px;
                resize:                     none;
                width:                      565px;
                margin-left:                8px;
            }
            .wp-disable-submit-checkbox {
                background:                 #fff none repeat scroll 0 0;
                border:                     1px solid #dbdbdb;
            }
            .wp-submit {
                margin:                     0 0 10px 4px;
                height:                     42px;
                width:                      100%;
                border:                     1px solid #dbdbdb;
                padding:                    10px;
                background:                 #fff;
            }
            #pay_whitepage_wrap {
                border:                     2px solid lightsteelblue;
                display:                    block;
                margin-bottom:              10px;
                padding:                    5px;
            }
        </style>
    </head>

    <body>
        <div class="wp-container container">
            <h2>Request for the Whitepage Iframe</h2>
            <textarea>
                <?php
                    $fields['password'] = str_repeat("*", strlen($fields['password']));
                    print_r($fields);
                ?>
            </textarea>
            <h2>Whitepage-Iframe</h2>
            <form method="post"
                  action="payframe/customer_out/<?php echo $authtoken;?>"
                  id="payment">
                <div id="pay_whitepage_wrap">
                    <!-- frame will be here -->
                </div>
                <div class="wp-customer-disable-submit-container">
                    <input id="disable_iframe_submit"
                        class="wp-disable-submit-checkbox"
                        type ="checkbox"
                    >
                    <span class="wp-disable-submit-text">
                        Disable Iframe Submit
                    </span>
                </div>
                <input class="btn wp-submit" type="submit" value="submit"/>
                <input type="hidden" value="{}">
            </form>
        </div>

        <script>
            // if pages content is loaded
            document.addEventListener('DOMContentLoaded', function() {
                /*
                 * init function to implement Whitepage Iframe to the wrapper
                 */
                payClient.init({
                    formId               : 'payment',
                    authtoken            : '<?php echo $authtoken; ?>',
                    // checkValidateCallback: checkValidateCallback,
                });

                /*
                 * on click event do activate / deactivate the submit event
                 * for the whitepage Iframe
                 */
                var disIfmChkId = 'disable_iframe_submit';
                document.getElementById(disIfmChkId).onclick = function (ev) {
                    if(ev.target.checked) {
                        payClient.toggleSubmit(false);
                    } else {
                        payClient.toggleSubmit(true);
                    }
                }
            }, false);


            /*
             * function for incomming callbacks
             *
             * @params
             *  internValid     boolean     validation check for all fields in the iframe
             *  cardtypes       object      cardtypes of the validate creditcard
             *  fields          object      contains the error validation messages
             *
             */
            function checkValidateCallback(internValid, cardtypes, fields) {
                var prefix = '[callbackCheckValid()]';
                console.log(prefix + 'allValid: ' + internValid);

                for(var i = 0; i < cardtypes.length; i++) {
                    if(cardtypes){
                        var id   = cardtypes[i].id;
                        var name = cardtypes[i].name;
                        console.log(prefix + 'cardid: '+id+', cardname: '+name);
                    }
                }

                for(var key in fields) {
                    var field = fields[key];

                    if(field.type === 'cc') {
                        var cardSettCls = '.wp-customer-creditcard-settings-container';
                        var cardSettBox = document.querySelector(cardSettCls);
                        cardSettBox.classList.remove('hide');
                    }

                    if(field != '') {
                        var ind = '-------------';
                        console.log(prefix+ind+' '+field.name+' '+ind);
                        console.log(prefix+'func        : '+field.func);
                        console.log(prefix+'isValid     : '+field.isValid);
                        console.log(prefix+'errorMessage: '+field.errorMessage);
                        console.log(prefix+ind+' '+field.name+' '+ind);
                        console.log('\n');
                    }
                }
            }
        </script>
    </body>
</html>
