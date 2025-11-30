// Books catalog page logic with category filters and pagination

// State management
let allBooks = [];
let filteredBooks = [];
let categories = [];
let pagination = new Pagination(5); // 5 items per page

// Initialize page
async function initBooksPage() {
  auth.requireAuth();
  
  await loadCategories();
  await loadBooks();
  setupEventListeners();
}

// Load categories from API
async function loadCategories() {
  try {
    const response = await api.category.getAll();
    categories = response.categories || [];
    
    populateCategoryDropdown();
  } catch (error) {
    console.error('Error loading categories:', error);
  }
}

// Populate category dropdown
function populateCategoryDropdown() {
  const categorySelect = document.getElementById('category-select');
  if (!categorySelect) return;

  // Clear existing options except the first one
  categorySelect.innerHTML = '<option value="">All Categories</option>';
  
  categories.forEach(category => {
    const option = document.createElement('option');
    option.value = category.id;
    option.textContent = category.name;
    categorySelect.appendChild(option);
  });
}

// Load books from API
async function loadBooks() {
  const loading = document.getElementById('loading');
  const booksGrid = document.getElementById('books-grid');
  const emptyState = document.getElementById('empty-state');

  loading.style.display = 'flex';
  booksGrid.innerHTML = '';
  emptyState.style.display = 'none';
  hideError('error-message');

  try {
    const books = await api.book.getAll();
    allBooks = books || [];
    filteredBooks = [...allBooks];
    
    pagination.setTotalItems(filteredBooks.length);
    pagination.reset();
    
    displayBooks();
  } catch (error) {
    console.error('Error loading books:', error);
    showError('error-message', 'Failed to load books. Please try again.');
  } finally {
    loading.style.display = 'none';
  }
}

// Apply filters
function applyFilters() {
  const searchInput = document.getElementById('search-input');
  const categorySelect = document.getElementById('category-select');
  const clearFiltersBtn = document.getElementById('clear-filters-btn');

  const searchTerm = searchInput.value.toLowerCase().trim();
  const categoryId = categorySelect.value;

  // Filter books
  filteredBooks = allBooks.filter(book => {
    // Search filter (title, author, ISBN)
    const matchesSearch = !searchTerm || 
      book.title.toLowerCase().includes(searchTerm) ||
      book.author.toLowerCase().includes(searchTerm) ||
      book.isbn.toLowerCase().includes(searchTerm);

    // Category filter
    const matchesCategory = !categoryId || 
      (book.category && book.category.id == categoryId);

    return matchesSearch && matchesCategory;
  });

  // Reset to first page and update
  pagination.setTotalItems(filteredBooks.length);
  pagination.reset();
  
  displayBooks();

  // Show/hide clear filters button
  if (clearFiltersBtn) {
    clearFiltersBtn.style.display = (searchTerm || categoryId) ? 'block' : 'none';
  }
}

// Clear all filters
function clearFilters() {
  const searchInput = document.getElementById('search-input');
  const categorySelect = document.getElementById('category-select');
  const clearFiltersBtn = document.getElementById('clear-filters-btn');

  searchInput.value = '';
  categorySelect.value = '';
  
  if (clearFiltersBtn) {
    clearFiltersBtn.style.display = 'none';
  }

  filteredBooks = [...allBooks];
  pagination.setTotalItems(filteredBooks.length);
  pagination.reset();
  
  displayBooks();
}

// Display books with pagination
function displayBooks() {
  const booksGrid = document.getElementById('books-grid');
  const emptyState = document.getElementById('empty-state');
  
  if (!booksGrid) return;

  // Update results count
  pagination.updateResultsCount('results-count');

  // Show empty state if no books
  if (filteredBooks.length === 0) {
    booksGrid.innerHTML = '';
    if (emptyState) {
      emptyState.style.display = 'block';
    }
    pagination.render('pagination', displayBooks);
    return;
  }

  if (emptyState) {
    emptyState.style.display = 'none';
  }

  // Get books for current page
  const booksToShow = pagination.getPageItems(filteredBooks);

  // Render books
  booksGrid.innerHTML = booksToShow.map(book => createBookCard(book)).join('');

  // Render pagination
  pagination.render('pagination', () => {
    displayBooks();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// Create book card HTML
function createBookCard(book) {
  const imageUrl = book.imageUrl || book.image || 'https://via.placeholder.com/300x400?text=No+Image';
  const available = book.available ? 'available' : 'unavailable';
  const statusText = book.available ? 'Available' : 'Reserved';
  const categoryName = book.category?.name || 'Uncategorized';

  return `
    <div class="book-card" onclick="viewBook('${escapeHtml(book.isbn)}')">
      <img src="${imageUrl}" 
           alt="${escapeHtml(book.title)}" 
           class="book-image" 
           onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
      <div class="book-info">
        <h3 class="book-title">${escapeHtml(book.title)}</h3>
        <p class="book-author"><strong>Author:</strong> ${escapeHtml(book.author)}</p>
        <p class="book-year"><strong>Year:</strong> ${book.year}</p>
        <p class="book-edition"><strong>Edition:</strong> ${book.edition}</p>
        <p class="book-category"><strong>Category:</strong> ${escapeHtml(categoryName)}</p>
        <span class="book-status ${available}">${statusText}</span>
        <button class="btn btn-primary" 
                onclick="event.stopPropagation(); viewBook('${escapeHtml(book.isbn)}')">
          View Details
        </button>
      </div>
    </div>
  `;
}

// Navigate to book detail page
function viewBook(isbn) {
  window.location.href = `book.html?isbn=${encodeURIComponent(isbn)}`;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Setup event listeners
function setupEventListeners() {
  // Filter form submission
  const filterForm = document.getElementById('filter-form');
  if (filterForm) {
    filterForm.addEventListener('submit', (e) => {
      e.preventDefault();
      applyFilters();
    });
  }

  // Real-time search with debounce
  const searchInput = document.getElementById('search-input');
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        applyFilters();
      }, 500); // Debounce 500ms
    });
  }

  // Category change
  const categorySelect = document.getElementById('category-select');
  if (categorySelect) {
    categorySelect.addEventListener('change', () => {
      applyFilters();
    });
  }

  // Clear filters button
  const clearFiltersBtn = document.getElementById('clear-filters-btn');
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', clearFilters);
  }

  // Logout button
  const logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      try {
        await api.user.logout();
        auth.logout();
      } catch (error) {
        auth.logout(); // Logout anyway
      }
    });
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBooksPage);
} else {
  initBooksPage();
}