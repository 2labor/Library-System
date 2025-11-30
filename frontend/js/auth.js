// Authentication utilities
const auth = {
  setUser(userData) {
    localStorage.setItem('user', JSON.stringify(userData));
  },

  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  setRegistrationData(data) {
    localStorage.setItem('registration', JSON.stringify(data));
  },

  // Get registration data
  getRegistrationData() {
    const data = localStorage.getItem('registration');
    return data ? JSON.parse(data) : null;
  },

  clearRegistrationData() {
    localStorage.removeItem('registration');
  },

  isLoggedIn() {
    return this.getUser() !== null;
  },

  logout() {
    localStorage.removeItem('user');
    window.location.href = '/frontend/pages/login.html';
  },

  // Redirect if not logged in
  requireAuth() {
    if (!this.isLoggedIn()) {
      window.location.href = '/frontend/pages/login.html';
      return false;
    }
    return true;
  },

  // Redirect if already logged in
  redirectIfLoggedIn() {
    if (this.isLoggedIn()) {
      window.location.href = '/frontend/pages/profile.html';
    }
  },
};