# Introduction #

Mollify supports guest mode, ie page that displays content without logging in. Administrator creates a guest user in Mollify administration utility, and configures guest mode for this user. The content shown for guest user is entirely customizable, as all regular folder publishing and permission rules apply.

# Configuration #

NOTE! Not yet available in version 2

1. Open admin util and create a new user account for the guest mode

2. Write down the user id in the user list

3. Configure user folders etc

4. Modify backend configuration.php, add following settings

```
	$SETTINGS = array(
		...
		"enable_guest_mode" => TRUE,
		"guest_user_id" => [GUEST_USER_ID_HERE]
	);
```

Remember to put the user id from step 2 into the settings

5. Create a guest page (for example by copying `index.html` into `guest.html`), and add following setting

```
	mollify.init({
		"guest-mode": true,
		...
	});
```

Now you can access your guest page by opening `guest.html`.