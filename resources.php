<?php
if(stristr(substr($locale, 0, 2), 'fr'))
{
    $app_name = "La plus grande FÊTE CHATONNE de l’histoire!";

    //Buttons
	$btnLike = "J’aime";
    $btnUnlike = "Je n’aime plus";
    $btnLiked = "Mentions J’aime";
    $btnLikePage = "J’aime cette page";
    $btnLikePhoto = "J’aime cette photo";
    $btnUnlikePhoto = "Je n’aime plus cette photo";
    $btn_install = "Install App";
    $btnShare = "Partager";
    $btnComment = "Commenter";
    $btnLikeComment = "J’aime ce commentaire";
    $btnUnlikeComment = "Je n’aime plus ce commentaire";
    $btn_upload = "Téléverser une photo";

    //Labels
    $lbl_privacy = "Cliquer ici pour lire la <a href='privacy.php' target='_blank'>politique sur la protection de la vie privée</a>.";
    $lblYouLike = "Vous aimez cela.";
    $lblYouAnd = "Vous et";
    $lblPeopleLike = "personnes aiment cela.";
    $lblPersonLikes = "personne aime cela.";
    $lbl_error_title = "An Error Occured";
    $lblWriteComment = "Écrire un commentaire...";
    $errorPermission = "You do not have authentication to comment on photo.";
    $timeStampString = "il y a quelques secondes";
    $lbl_photos_instructions = "Please select a photo to upload.";
    $lbl_Uploadtitle = "Téléverser à partir de mon ordinateur";
    $lbl_UploadInstructions = "Choisissez une photo dans votre ordinateur et cliquez sur le bouton « Téléverser une photo » pour l’afficher sur le babillard de la fête chatonne.";
    $error_upload = "Vous devez choisir une photo pour continuer.";

    //Images
    $img_header = "header-fr.jpg";
    $img_uploadPhoto = "btn-upload-fr.png";
    $img_adFbPhoto = "btn-fb-album-fr.png";
    $alt_header = "La plus grande FÊTE CHATONNE de l’histoire!";
    $alt_uploadPhoto = "Téléverser une photo";
    $alt_adFbPhoto = "Ajouter une photo de Facebook";

    //Primary Navigation
    $lbl_sort = "Choisir  :";
    $opt_all = "Toutes les photos";
    $opt_top = "Les meilleures photos";
    $opt_my = "Mes photos";
    $opt_friends = "Photos de mes amis";
    $opt_royale = "Photos des Chatons ROYALE";
    
    //page Install app 
    $lbl_title_install = "This Facebook Application Requires Authentication";
    $lbl_instructions_install = "Afin de vous permettre de profiter au maximum de cette fête chatonne, vous devez donner votre permission à la présente application pour qu’elle puisse accéder à vos renseignements et photos de base sur Facebook.<br><br>
    EN OUTRE, comme cette fête chatonne est trop VASTE pour se dérouler en un seul endroit, les photos que vous téléverserez sur Facebook seront également affichées sur le babillard PINTEREST de la fête chatonne : <a href='http://pinterest.com/royalepinterest/' target='_blank'>http://pinterest.com/royalepinterest/</a>";
    
    //page album
    $lbl_title_album = "Choose one of your albums";
    $lbl_albums_album = "My Albums";

    //page photos
	$lbl_title_photos = "Choose your photos";
	$btn_back = "Retour à l’album";
	$btn_back_albums = "Retour aux albums";
    $btn_add = "Ajouter une photo";
    $lbl_title_preview = "Prévisualiser";
   
    //Upload photo
    $lbl_title_upload = "File has been uploaded";
    $lbl_session_title = "Session is dropped";
    $lbl_session_description = "Your Facebook session has expired. Please try loading the page again. If the problem persists please contact <a href='mailto:royale@modernmedia.ca'>royale@modernmedia.ca</a>.";
    
    //Other messages
    $msg_no_photos = "Vous n’avez pas encore affiché de photos pour la Fête chatonne. Pour vous joindre à la fête, vous n’avez qu’à téléverser vos photos préférées de chatons de votre ordinateur ou de vos albums sur Facebook!";
    $msg_no_friend_photos = "Aucun de vos amis n’a encore téléversé de photos pour la Fête chatonne. ";    
    $msg_FileTooLarge = "Fichier trop volumineux. Le fichier doit contenir moins de 15 mégaoctets.";
    $msg_FileTypeInvalid = "Type de fichier invalide. Seuls les formats JPG, GIF et PNG sont acceptés.";
    $msg_no_friend_photos_btn = "Invitez-les à le faire dès maintenant!";

    //Share
    $share_name = "La plus grande fête chatonne de l’histoire";
    $share_caption = "Invitation à tous les amateurs de chatons et de chats!";
    $share_message = " has uploaded a photo to https://www.facebook.com/photo.php?fbid=";
    $share_description = "J’ai téléversé une photo de chaton pour la PLUS GRANDE FÊTE CHATONNE DE L’HISTOIRE. Joignez-vous à la FÊTE et cliquez sur « J’AIME » si vous croyez que MON invité à la fête est un des chats les plus formidables sur place!";
    $share_description2 = "Joignez-vous à la FÊTE et cliquez sur « J’AIME » si vous croyez que MON invité à la fête est un des chats les plus formidables sur place!";
    
    $ShareTitle = "La plus grande fête chatonne de l’histoire";
    $ShareCaption = "Invitation à tous les amateurs de chatons et de chats!";
    $ShareDescription = "Je viens de me joindre à la plus grande fête chatonne de l’histoire! Célébrez avec les Chatons ROYALE en ajoutant VOS photos de chatons préférées à la fête.";
    $ShareLinkTitle = "La plus grande fête chatonne de l’histoire";
    
    //DateTime
    $year = "années";
    $month = "mois";
    $day = "jour";
    $hour = "heure";
    $minute = "minute";
    $second = "seconde";

} else {
    $app_name = "The Biggest KITTEN PARTY ever!";
    
	//Buttons
	$btnLike = "Like";
    $btnUnlike = "Unlike";
    $btnLiked = "Liked";
    $btnLikePage = "Like This Page";
    $btnLikePhoto = "Like this photo";
    $btnUnlikePhoto = "Unlike this photo";
    $btn_install = "Install App";
    $btnShare = "Share";
    $btnComment = "Comment";
    $btnLikeComment = "Like this comment";
    $btnUnlikeComment = "Unlike this comment";
    $btn_upload = "Upload Photo";
    
    //Labels
    $lbl_privacy = "Click here to read the <a href='privacy.php' target='_blank'>Privacy Policy</a>.";
    $lblYouLike = "You like this.";
    $lblYouAnd = "You and";
    $lblPeopleLike = "people like this.";
    $lblPersonLikes = "person likes this.";
    $lbl_error_title = "An Error Occured";
    $lblWriteComment = "Write a comment...";
    $errorPermission = "You do not have authentication to comment on photo.";
    $timeStampString = "a few seconds ago";
    $lbl_photos_instructions = "Please select a photo to upload.";
    $lbl_Uploadtitle = "Upload from my computer";
    $lbl_UploadInstructions = "Select a photo from your computer and click the Upload Photo button to add it to the Kitten Party Board";
    $error_upload = "Need to select a photo to proceed.";
    

    //Images
    $img_header = "header-en.jpg";
    $img_uploadPhoto = "btn-upload-en.png";
    $img_adFbPhoto = "btn-fb-album-en.png";
    $alt_header = "The Biggest KITTEN PARTY ever!";
    $alt_uploadPhoto = "Upload Photo";
    $alt_adFbPhoto = "Add Photo from Facebook";

    //Primary Navigation
    $lbl_sort = "Sort By:";
    $opt_all = "All Photos";
    $opt_top = "Top Photos";
    $opt_my = "My Photos";
    $opt_friends = "Friends Photos";
    $opt_royale = "ROYALE Kitten Photos";
    
    //page Install app 
    $lbl_title_install = "This Facebook Application Requires Authentication";
    $lbl_instructions_install = "To enable you to participate fully in the Kitten Party experience, this app needs your permission to access to your basic Facebook info and photos.<br><br>
    AND, because this Kitten Party is too BIG to be contained in just one place, when you upload your kitten photos, they will also be visible on our PINTEREST Party Board: <a href='http://pinterest.com/royalepinterest/' target='_blank'>http://pinterest.com/royalepinterest/</a>";
    
    //page album
    $lbl_title_album = "Choose one of your albums";
    $lbl_albums_album = "My Albums";

    //page photos
	$lbl_title_photos = "Choose your photos";
	$btn_back = "Back to albums";
	$btn_back_albums = "Back to albums";
    $btn_add = "Add photo";
    $lbl_title_preview = "Preview";
   
    //Upload photo
    $lbl_title_upload = "File has been uploaded";
    $lbl_session_title = "Session is dropped";
    $lbl_session_description = "Your Facebook session has expired. Please try loading the page again. If the problem persists please contact <a href='mailto:royale@modernmedia.ca'>royale@modernmedia.ca</a>.";
    
    //Other messages
    $msg_no_photos = "You have not posted any photos to the Kitten Party yet. To join the Party, simply upload your favourite kitten pictures from your computer or Facebook albums!";
    $msg_no_friend_photos = "None of your friends have uploaded pictures to the Kitten Party yet.";
    $msg_FileTooLarge = "File too large. File must be less than 15 megabytes.";
    $msg_FileTypeInvalid = "Invalid file type. Only JPG, GIF and PNG types are accepted.";
    $msg_no_friend_photos_btn = "Invite them now!";

    //Share
    $share_name = "Biggest Kitten Party Ever";
    $share_caption = "Inviting all Kitten & Cat Lovers!";
    $share_message = " has uploaded a photo to https://www.facebook.com/photo.php?fbid=";
    $share_description = "I just uploaded a Kitten Photo to the BIGGEST KITTEN PARTY EVER. Join the PARTY and ‘LIKE’ my photo if you think MY Party Guest is one of the coolest cats there!";
    $share_description2 = "Join the PARTY and ‘LIKE’ my photo if you think MY Party Guest is one of the coolest cats there!";
    
    $ShareTitle = "Biggest Kitten Party Ever";
    $ShareCaption = "Inviting all Kitten & Cat Lovers!";
    $ShareDescription = "I just joined the Biggest Kitten Party Ever! Celebrate with the ROYALE Kittens by bringing YOUR favourite kitten photos to the Party.";
    $ShareLinkTitle = "Biggest Kitten Party Ever";

    //DateTime
    $year = "year";
    $month = "month";
    $day = "day";
    $hour = "hour";
    $minute = "minute";
    $second = "second";
}
?>