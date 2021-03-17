let magic = new Magic(magic_wp.publishable_key_0);
let authorized = false;
const authorizedTemplate = settings.templates.authorized;
const unauthorizedTemplate = settings.templates.unauthorized;

// Magic Sign-in
const MagicSignIn = async () => {
  let html = "";

  if (window.location.pathname === magic_wp.redirect_uri_0) {
    try {
      await magic.auth.loginWithCredential();
      const userMetadata = await magic.user.getMetadata();
      html = authorizedTemplate;
    } catch {
      window.location.href = window.location.origin;
    }
  } else {
    const isLoggedIn = await magic.user.isLoggedIn();

    html = unauthorizedTemplate;

    if (isLoggedIn) {
      const userMetadata = await magic.user.getMetadata();
      html = authorizedTemplate;
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

document.addEventListener("DOMContentLoaded", function (event) {
  if (jQuery("#magic-sign-in").length > 0) {
    MagicSignIn();
  }
});

// Magic Private
const MagicPrivate = async () => {
  let html = "";

  const isLoggedIn = await magic.user.isLoggedIn();
  if (!isLoggedIn) {
    html = unauthorizedTemplate;
  }

  if (document.getElementById("magic-private")) {
    document.querySelector("#magic-private").innerHTML = html;
  }
};

document.addEventListener("DOMContentLoaded", function (event) {
  if (jQuery("#magic-private").length > 0) {
    return MagicPrivate();
  }
});
