<?php

//This is for the player id that players must set on the website

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This should need no shortcode attributes
function vidyen_mmo_loa_id_func($atts)
{

  //Simple attribue to see if you can edit. But default will be off.
  $atts = shortcode_atts(
    array(
        'edit' => FALSE
    ), $atts, 'vyps-loa-query' );

  //Boot user out if not logged in. BTW my code is evolving. -Felty
  if ( ! is_user_logged_in() )
  {
    return; //Not logged in. You see nothing.
  }

  //Get current user Id obviously and email
  $user_id = get_current_user_id();
  $user_info = get_userdata($user_id);
  $user_email =  $user_info->user_email;
  $user_registration_link = '/registerid '. $user_email;
  $loa_link_generate_url = plugins_url( 'js/', dirname(__FILE__) ) . 'loa-id-generate.js';

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vidyen_mmo_loa_id';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //I'm only going to run this is you edit. Otherwise its just a clear
  if ($atts['edit'] == TRUE)
  {
    if (isset($_POST['update_loa_id']))
    {
      //As the post is the only thing that edits data, I suppose this is the best place to the noce
      $vyps_nonce_check = $_POST['vypsnoncepost'];

      //Make sure no one is being tricked into updating their Wallet Address
      if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) )
      {
          // This nonce is not valid.
          die( 'Security check' );
      }
      else
      {
          // The nonce was valid.
          //Carry on.
          //Do the dew.
          //Do we even need a else here?
      }

      //Getting the loa_id and things.
      $loa_id = sanitize_text_field($_POST['loa_id']); //Sanitize it! From orbit!

      //Ok now we update in hell! (Hell = usermeta table) I wonder if anyone reads these coments. -Felty
      update_user_meta( $user_id, $key, $loa_id );
    }

    //NOTE: This should come after the post so it will update before the refresh or otherwise the loa_id may be out of date. Remind me to check.
    //And we have the current loa_id. This may not exist or be blank. Also I'm 99.99999999% sure I can't tell if its actually a real XMR address
    $loa_id = get_user_meta( $user_id, $key, $single );

    //Adding a nonce to the post
    $vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

    //displaying loa_id if EXISTS
    if (strlen($loa_id) > 1)
    {
      $display_loa_id = "Current LoA ID:<br>" . $loa_id; //I'm really guessing here as just assuming that if they put in more than one. Probaly should do validation somewhere down road
    }
    else
    {
      $display_loa_id = "No LoA ID set:"; //Just some text
    }

    $form_result_ouput = "
      <div>
          <p>$display_loa_id</p>
          <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
            <table class=\"form-table\">
              <tbody>
                <tr class=\"form-field form-required\">
                  <th scope=\"row\">
                    <label for=\"loa_id\">Update LoA ID:</label>
                  </th>
                  <td>
                    <input name=\"loa_id\" type=\"text\" id=\"loa_id\" value=\"$loa_id\" aria-required=\"true\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"256\">
                  </td>
                </tr>
              </tbody>
            </table>
            <p class=\"submit\">
            <input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
            <input type=\"submit\" name=\"update_loa_id\" id=\"update_loa_id\" class=\"button button-primary\" value=\"Update\">
          </p>
        </form>
      </div>
      ";
    }
    else
    {
      //This is all other cases... ALl you can do is clear.
      if (isset($_POST['clear_loa_id_action']))
      {
        //As the post is the only thing that edits data, I suppose this is the best place to the noce
        $vyps_nonce_check = $_POST['vypsnoncepost'];

        //Make sure no one is being tricked into updating their Wallet Address
        if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) )
        {
            // This nonce is not valid.
            die( 'Security check' );
        }

        //Getting the loa_id and things.
        //$loa_id = sanitize_text_field($_POST['loa_id']); //Sanitize it! From orbit!
        $loa_id = ''; //A hard coded blank.

        //Ok now we update in hell! (Hell = usermeta table) I wonder if anyone reads these coments. 300 was a good movie. -Felty
        update_user_meta( $user_id, $key, $loa_id );
      }

      //Adding a nonce to the post
      $vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );

      $loa_id = get_user_meta( $user_id, $key, $single ); //I forgot this. Shoudl work now.

      //displaying loa_id if EXISTS
      if (strlen($loa_id) > 1)
      {
        $display_loa_id =  $loa_id; //I'm really guessing here as just assuming that if they put in more than one. Probaly should do validation somewhere down road
      }
      else
      {
        $display_loa_id = "No LoA ID set!"; //Just some text
      }

      $form_result_ouput =
      "<div>
          <form method=\"post\" name=\"createuser\" id=\"createuser\" class=\"validate\" novalidate=\"novalidate\" enctype=\"multipart/form-data\">
            <table class=\"form-table\">
              <tbody>
                <tr class=\"form-field form-required\">
                  <th scope=\"row\">
                    <label for=\"loa_id\">LoA ID:</label>
                  </th>
                  <td>
                    <input name=\"loa_id\" type=\"text\" id=\"loa_id\" value=\"$display_loa_id\" autocapitalize=\"none\" autocorrect=\"off\" maxlength=\"256\" disabled>
                  </td>
                </tr>
              </tbody>
            </table>
            <p class=\"submit\">
            <input type=\"hidden\" name=\"vypsnoncepost\" id=\"vypsnoncepost\" value=\"$vyps_nonce_check\" />
            <input type=\"hidden\" name=\"clear_loa_id_action\" id=\"clear_loa_id_action\" value=\"clear_loa_id_action\" />
            <input type=\"submit\" name=\"clear_loa_id\" id=\"clear_loa_id\" class=\"button button-primary\" value=\"Clear ID\" onclick=\"return confirm('Are you sure you want to clear your ID? It will have to be re-registered from game client again.');\">
          </p>
        </form>
      </div>
        ";
    }

  $form_result_ouput .= '<table>
                  <tr>
                    <td>
                      <div>To link your LOA account copy and paste the command below into the chat in Legends of Aria on the City States of Dandolo Game server.</div>
                    </td>
                  </tr>
                  <tr>
                     <td>
                      <input id="url_output" style="width: 100%; padding: 12px 20px; margin: 8px 0; box-sizing: border-box;" type="text" value="'.$user_registration_link.'" width="100%" readonly>
                      <button onclick="copy_link()">Copy</button>
                    </td>
                   </tr>
                  </table>
                  <script src="'.$loa_link_generate_url.'"></script>';

  //Remember kids. Always return shortcodes. Never echo or you are going to have a bad time.
  return $form_result_ouput;
}

//Adding the shortcode.
add_shortcode( 'vidyen-loa-id', 'vidyen_mmo_loa_id_func');

//Some debuging going on here.
function vidyen_load_id_shortcode_func($atts)
{

  $atts = shortcode_atts(
		array(
				'loa_id' => 'empty'
		), $atts, 'vyps-loa-query' );

    $loa_user_id = $atts['loa_id'];

    return vidyen_mmo_loa_user_query_func($loa_user_id);
}

add_shortcode( 'vyps-loa-query', 'vidyen_load_id_shortcode_func');
