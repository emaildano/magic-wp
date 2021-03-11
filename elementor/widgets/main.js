let magic = new Magic(magic_wp.publishable_key_0);

/* Render Sign-in. */
const MagicSignIn = async () => {
  let html = '';

  if (window.location.pathname === magic_wp.redirect_uri_0) {
    try {
      /* Complete the "authentication callback" */
      await magic.auth.loginWithCredential();

      /* Get user metadata including email */
      const userMetadata = await magic.user.getMetadata();

      html = settings.templates.authorized;
    } catch {
      window.location.href = window.location.origin;
    }
  } else {
    const isLoggedIn = await magic.user.isLoggedIn();

    /* Show login form if user is not logged in */
    html = settings.templates.unauthorized;

    if (isLoggedIn) {
      // window.location = "https://example.com";
      /* Get user metadata including email */
      const userMetadata = await magic.user.getMetadata();
      html = settings.templates.authorized;
    }
  }

  if (document.getElementById("magic-sign-in")) {
    document.getElementById("magic-sign-in").innerHTML = html;
  }
};

/* Login Handler */
const handleLogin = async (e) => {
  e.preventDefault();
  const email = new FormData(e.target).get("email");
  const redirectURI = `${window.location.origin + magic_wp.redirect_uri_0}`;
  if (email) {
    await magic.auth.loginWithMagicLink({ email, redirectURI });
    render();
  }
};

/* Logout Handler */
const handleLogout = async () => {
  await magic.user.logout();
  render();
};

MagicSignIn();
