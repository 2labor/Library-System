// Utility functions

// Show error message
function showError(elementId, message) {
  const element = document.getElementById(elementId);
  if (element) {
    element.textContent = message;
    element.style.display = 'block';
  }
}

// Hide error message
function hideError(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.style.display = 'none';
  }
}

// Show success message
function showSuccess(elementId, message) {
  const element = document.getElementById(elementId);
  if (element) {
    element.textContent = message;
    element.style.display = 'block';
  }
}

// Format date
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-En', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

// Validate email
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// Validate password
function validatePassword(password) {
  return password.length >= 6;
}

// Show loading state
function setLoading(buttonId, isLoading) {
  const button = document.getElementById(buttonId);
  if (!button) return;

  if (!button.dataset.originalText) {
    button.dataset.originalText = button.textContent;
  }

  if (isLoading) {
    button.disabled = true;
    button.textContent = 'Loading...';
  } else {
    button.disabled = false;
    button.textContent = button.dataset.originalText;
  }
}


// Create book card HTML
function createBookCard(book) {
  console.log(book);
  return `
    <div class="book-card" data-isbn="${book.isbn}">
      <img src="${book.imageUrl || 'https://via.placeholder.com/150x200?text=No+Image'}" 
           alt="${book.title}" 
           class="book-image">
      <div class="book-info">
        <h3 class="book-title">${book.title}</h3>
        <p class="book-author">Author: ${book.author}</p>
        <p class="book-year">Year: ${book.year}</p>
        <p class="book-edition">Edition: ${book.edition}</p>
        <p class="book-category">Category: ${book.category?.name || 'No category'}</p>
        <span class="book-status ${book.available ? 'available' : 'unavailable'}">
          ${book.available ? 'Available' : 'Reserved'}
        </span>
        <button class="btn btn-primary" onclick="viewBook('${book.isbn}')">
          More information
        </button>
      </div>
    </div>
  `;
}

// Navigate to page
function navigateTo(page) {
  window.location.href = page;
}

// Get query parameter
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}