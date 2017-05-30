<!DOCTYPE html>
    <head>
        <?php
            $username = 'username';
            $password = 'password';
            $data     = array(
                "partner_data" => array(
                    'name'=>'ypsilon'
                )
            );
            $data_string = json_encode($data);
            $url = 'https://f1-norrisdev1-tmp.infosys.de/tokens/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
                )
            );

            $result    = curl_exec($ch);
            $nrsAnswer = json_decode($result, true);
            $jsurl     = $nrsAnswer['jsurl'];
            $authtoken = $nrsAnswer['authtoken'];

            echo "<pre>data_string: "; print_r($data_string); echo "</pre>";
            echo "<pre>nrsAnswer  : "; print_r($nrsAnswer); echo "</pre>";
            echo "<pre>authtoken  : "; print_r($authtoken); echo "</pre>";
        ?>
        <script type="text/javascript" charset="utf8" src="<?php echo $jsurl; ?>"></script>
    </head>
    <body>
        <div style="width:400px;" class="container">
            <form onsubmit="" method="post" action="" id="payment">
                <h2>Payment</h2>
                <table>
                    <tbody>
                        <tr>
                            <td>Card Type</td>
                            <td>
                                <select id="select_cc" name="creditcard">
                                    <option value="VI">Visa</option>
                                    <option value="CA">Mastercard</option>
                                    <option value="AX">American Express</option>
                                    <option value="DC">Diners Club</option>
                                    <option value="AP">Airplus</option>
                                    <option value="JCB">Japan Credit Bureau</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Credit Card</td>
                            <td colspan="2" style="height:32px">
                                <div id="nrs_cc_form" style="height:100%;width:100%">
                                    <!-- iframe will be here -->
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Card Validation Code</td>
                            <td colspan="2" style="height:32px">
                                <div id="nrs_cvc_form" style="height:100%;width:100%">
                                    <!-- iframe will be here -->
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Expire Date</td>
                            <td><input value="20.01.2015" type="text" name="dateto"></td>
                        </tr>
                        <tr>
                            <td>Ref. Nummer</td>
                            <td><input value="123456789" type="text" name="ref"></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td><input value="23.67" type="text" name="amount"></td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <div>
                        <input type="submit" style="margin-left:91px;" value="submit" class="btn"/>
                    </div>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            nrsClient.init({
                debug       : false,
                formId      : 'payment',
                authtoken   : "<?php echo $authtoken;?>",
                styleFile   : '/assets/css/bootstrap.css',
                checkValid  : checkValidationAllIframefields,
                cc: {
                    'wrapId': 'nrs_cc_form',
                    'nameId': 'form_cc',
                    'validate': true
                },
                cvc: {
                    'wrapId': 'nrs_cvc_form',
                    'nameId': 'form_cvc',
                    'validate': true
                },
            });

            function checkValidationAllIframefields(internValid, cardtypes, messages) {
                console.log('test.template.html: valid = ' + internValid);

                for(var i = 0; i < cardtypes.length; i++){
                    if(cardtypes){
                        console.log('cardid: ' + cardtypes[i].id + ', cardname: ' + cardtypes[i].name);
                    }
                }

                for (var key in messages){
                    if(messages[key] != ''){
                        console.log('test.template.html: ' + messages[key]);
                    }
                }
            }
        </script>
    </body>
</html>
