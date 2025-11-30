// Pagination utility class

class Pagination {
  constructor(itemsPerPage = 5) {
    this.itemsPerPage = itemsPerPage;
    this.currentPage = 1;
    this.totalItems = 0;
  }

  // Get total number of pages
  getTotalPages() {
    return Math.ceil(this.totalItems / this.itemsPerPage);
  }

  // Get items for current page
  getPageItems(items) {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    return items.slice(startIndex, endIndex);
  }

  // Get page range info
  getPageInfo() {
    const totalPages = this.getTotalPages();
    const start = (this.currentPage - 1) * this.itemsPerPage + 1;
    const end = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
    
    return {
      start,
      end,
      total: this.totalItems,
      currentPage: this.currentPage,
      totalPages
    };
  }

  // Go to specific page
  goToPage(page) {
    const totalPages = this.getTotalPages();
    
    if (page < 1) {
      this.currentPage = 1;
    } else if (page > totalPages) {
      this.currentPage = totalPages;
    } else {
      this.currentPage = page;
    }
    
    return this.currentPage;
  }

  // Go to next page
  nextPage() {
    return this.goToPage(this.currentPage + 1);
  }

  // Go to previous page
  previousPage() {
    return this.goToPage(this.currentPage - 1);
  }

  // Reset to first page
  reset() {
    this.currentPage = 1;
  }

  // Set total items
  setTotalItems(total) {
    this.totalItems = total;
  }

  // Generate page numbers array with ellipsis
  getPageNumbers() {
    const totalPages = this.getTotalPages();
    const current = this.currentPage;
    const pages = [];
    
    if (totalPages <= 7) {
      // Show all pages if 7 or fewer
      for (let i = 1; i <= totalPages; i++) {
        pages.push(i);
      }
    } else {
      // Always show first page
      pages.push(1);
      
      // Add ellipsis if needed
      if (current > 3) {
        pages.push('...');
      }
      
      // Show pages around current page
      const start = Math.max(2, current - 1);
      const end = Math.min(totalPages - 1, current + 1);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      // Add ellipsis if needed
      if (current < totalPages - 2) {
        pages.push('...');
      }
      
      // Always show last page
      pages.push(totalPages);
    }
    
    return pages;
  }

  // Render pagination HTML
  render(containerId, onPageChange) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const totalPages = this.getTotalPages();
    
    // Hide pagination if only one page or no items
    if (totalPages <= 1) {
      container.style.display = 'none';
      return;
    }

    container.style.display = 'flex';
    container.innerHTML = '';

    // Previous button
    const prevBtn = this.createButton('←', this.currentPage === 1, () => {
      this.previousPage();
      onPageChange();
    });
    container.appendChild(prevBtn);

    // Page numbers
    const pageNumbers = this.getPageNumbers();
    
    pageNumbers.forEach(pageNum => {
      if (pageNum === '...') {
        const ellipsis = this.createEllipsis();
        container.appendChild(ellipsis);
      } else {
        const pageBtn = this.createButton(
          pageNum,
          false,
          () => {
            this.goToPage(pageNum);
            onPageChange();
          },
          pageNum === this.currentPage
        );
        container.appendChild(pageBtn);
      }
    });

    // Next button
    const nextBtn = this.createButton('→', this.currentPage === totalPages, () => {
      this.nextPage();
      onPageChange();
    });
    container.appendChild(nextBtn);

    // Page info
    const pageInfo = document.createElement('div');
    pageInfo.className = 'pagination-info';
    pageInfo.textContent = `Page ${this.currentPage} of ${totalPages}`;
    container.appendChild(pageInfo);
  }

  // Create pagination button
  createButton(text, disabled, onClick, active = false) {
    const btn = document.createElement('button');
    btn.className = `pagination-btn ${active ? 'active' : ''}`;
    btn.textContent = text;
    btn.disabled = disabled;
    
    if (!disabled && !active) {
      btn.onclick = onClick;
    }
    
    return btn;
  }

  // Create ellipsis element
  createEllipsis() {
    const ellipsis = document.createElement('span');
    ellipsis.className = 'pagination-btn ellipsis';
    ellipsis.textContent = '...';
    return ellipsis;
  }

  // Update results count display
  updateResultsCount(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const info = this.getPageInfo();
    
    if (this.totalItems === 0) {
      element.textContent = 'No books found';
    } else {
      element.textContent = `Showing ${info.start}-${info.end} of ${info.total} books`;
    }
  }
}