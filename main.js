/* Magic Instance */
let magic = new Magic(magic_wp.publishable_key_0);

/* Render Function */
const render = async () => {
  let html = "";

  if (window.location.pathname === magic_wp.redirect_uri_0) {
    try {
      /* Complete the "authentication callback" */
      await magic.auth.loginWithCredential();

      /* Get user metadata including email */
      const userMetadata = await magic.user.getMetadata();

      html = `
         <h1>Current user: ${userMetadata.email}</h1>
         <button onclick="handleLogout()">Logout</button>
       `;
    } catch {
      window.location.href = window.location.origin;
    }
  } else {
    const isLoggedIn = await magic.user.isLoggedIn();

    /* Show login form if user is not logged in */
    html = `
       <h1>Please sign up or login</h1>
       <form onsubmit="handleLogin(event)">
         <input type="email" name="email" required="required" placeholder="Enter your email" />
         <button type="submit">Send</button>
       </form>
     `;

    if (isLoggedIn) {
      /* Get user metadata including email */
      const userMetadata = await magic.user.getMetadata();
      html = `
         <h1>Current user: ${userMetadata.email}</h1>
         <button onclick="handleLogout()">Logout</button>
       `;
    }
  }

  if (document.getElementById("magic")) {
    document.getElementById("magic").innerHTML = html;
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

render();
