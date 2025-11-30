// API Configuration
const API_BASE_URL = 'http://localhost:8000'; // Change to your backend URL

// API Helper Functions
const api = {
  // Generic request handler
  async request(endpoint, options = {}) {
    const config = {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || 'Request failed');
      }

      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  },

  // Account endpoints
  account: {
    register(data) {
      return api.request('/accounts', {
        method: 'POST',
        body: JSON.stringify(data),
      });
    },

    verifyEmail(email, code) {
      return api.request('/accounts/verify', {
        method: 'POST',
        body: JSON.stringify({ email, code }),
      });
    },

    resetPassword(email) {
      return api.request('/accounts/reset', {
        method: 'POST',
        body: JSON.stringify({ email }),
      });
    },

    resetPasswordWithToken(token, new_password) {
      return api.request('/accounts/reset/token', {
        method: 'POST',
        body: JSON.stringify({ token, new_password }),
      });
    },

    getById(id) {
      return api.request(`/accounts/${id}`, {
        method: 'GET',
      });
    },

    delete(id) {
      return api.request(`/accounts/${id}`, {
        method: 'DELETE',
      });
    },
  },

  // User endpoints
  user: {
    create(data) {
      return api.request('/users', {
        method: 'POST',
        body: JSON.stringify(data),
      });
    },

    login(login, password) {
      return api.request('/users/login', {
        method: 'POST',
        body: JSON.stringify({ login, password }),
      });
    },

    logout() {
      return api.request('/users/logout', {
        method: 'POST',
      });
    },

    getById(id) {
      return api.request(`/users/${id}`, {
        method: 'GET',
      });
    },

    update(data) {
      return api.request('/users', {
        method: 'PUT',
        body: JSON.stringify(data),
      });
    },

    delete(id) {
      return api.request(`/users/${id}`, {
        method: 'DELETE',
      });
    },
  },

  // Book endpoints
  book: {
    getAll() {
      return api.request('/books', {
        method: 'GET',
      });
    },

    find(criteria) {
      const params = new URLSearchParams(criteria);
      return api.request(`/books?${params}`, {
        method: 'GET',
      });
    },

    add(data) {
      return api.request('/books', {
        method: 'POST',
        body: JSON.stringify(data),
      });
    },

    update(isbn, data) {
      return api.request(`/books/${isbn}`, {
        method: 'PUT',
        body: JSON.stringify(data),
      });
    },

    delete(isbn) {
      return api.request(`/books/${isbn}`, {
        method: 'DELETE',
      });
    },

    toggleAvailability(isbn) {
      return api.request(`/books/${isbn}`, {
        method: 'PATCH',
      });
    },
  },

  // Category endpoints
  category: {
    getAll() {
      return api.request('/categories', {
        method: 'GET',
      });
    },

    getById(id) {
      return api.request(`/categories/${id}`, {
        method: 'GET',
      });
    },
  },

  // Reservation endpoints
  reservation: {
    reserve(isbn, userId) {
      return api.request('/reservation/reserve', {
        method: 'POST',
        body: JSON.stringify({ isbn, userId }),
      });
    },

    extend(isbn) {
      return api.request(`/reservation/extend?isbn=${isbn}`, {
        method: 'PUT',
      });
    },

    cancel(id) {
      return api.request(`/reservation/cancel?id=${id}`, {
        method: 'DELETE',
      });
    },

    getByBook(isbn) {
      return api.request(`/reservation/book/${isbn}`, {
        method: 'GET',
      });
    },

    getByUser(userId) {
      return api.request(`/reservation/user/${userId}`, {
        method: 'GET',
      });
    },
  },
};