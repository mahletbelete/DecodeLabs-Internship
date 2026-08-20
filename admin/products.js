const form           = document.getElementById('product-form');
const formHeading    = document.getElementById('form-heading');
const submitBtn      = document.getElementById('form-submit-btn');
const cancelBtn      = document.getElementById('form-cancel-btn');
const formMessage    = document.getElementById('product-form-message');
const productIdInput = document.getElementById('product-id');

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const d = btn.dataset;

        productIdInput.value                           = d.id;
        document.getElementById('p-name').value        = d.name;
        document.getElementById('p-description').value = d.description;
        document.getElementById('p-price').value       = d.price;
        document.getElementById('p-category').value    = d.category;
        document.getElementById('p-image').value       = d.image;
        document.getElementById('p-available').checked = d.available === '1';

        formHeading.textContent = 'Edit Product';
        submitBtn.textContent   = 'Save Changes';
        cancelBtn.style.display = 'inline-block';

        document.getElementById('product-form-section').scrollIntoView({ behavior: 'smooth' });
    });
});

cancelBtn.addEventListener('click', resetForm);

function resetForm() {
    form.reset();
    productIdInput.value    = '';
    formHeading.textContent = 'Add New Product';
    submitBtn.textContent   = 'Add Product';
    cancelBtn.style.display = 'none';
    formMessage.textContent = '';
    formMessage.className   = 'form-message';
    document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; });
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const id          = productIdInput.value;
    const name        = document.getElementById('p-name').value.trim();
    const description = document.getElementById('p-description').value.trim();
    const price       = parseFloat(document.getElementById('p-price').value);
    const category    = document.getElementById('p-category').value.trim();
    const image       = document.getElementById('p-image').value.trim();
    const available   = document.getElementById('p-available').checked ? 1 : 0;
    let valid         = true;

    if (!name) {
        showFieldError('p-name-error', 'Name is required.');
        valid = false;
    }
    if (!category) {
        showFieldError('p-category-error', 'Category is required.');
        valid = false;
    }
    if (isNaN(price) || price <= 0) {
        showFieldError('p-price-error', 'A valid price is required.');
        valid = false;
    }

    if (!valid) return;

    const isEdit = id !== '';
    const url    = isEdit ? `../php/products.php?id=${id}` : '../php/products.php';
    const method = isEdit ? 'PUT' : 'POST';

    submitBtn.disabled    = true;
    submitBtn.textContent = isEdit ? 'Saving...' : 'Adding...';

    try {
        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, description, price, category, image, available })
        });

        const data = await response.json();

        if (data.success) {
            formMessage.textContent = data.message;
            formMessage.className   = 'form-message success';
            setTimeout(() => { window.location.reload(); }, 800);
        } else {
            formMessage.textContent = data.message || 'Something went wrong.';
            formMessage.className   = 'form-message error';
            submitBtn.disabled      = false;
            submitBtn.textContent   = isEdit ? 'Save Changes' : 'Add Product';
        }

    } catch (err) {
        formMessage.textContent = 'Could not connect to the server.';
        formMessage.className   = 'form-message error';
        submitBtn.disabled      = false;
        submitBtn.textContent   = isEdit ? 'Save Changes' : 'Add Product';
    }
});

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.id;

        if (!confirm('Delete this product? This cannot be undone.')) return;

        btn.disabled = true;

        try {
            const response = await fetch(`../php/products.php?id=${id}`, { method: 'DELETE' });
            const data     = await response.json();

            if (data.success) {
                const row = document.getElementById(`row-${id}`);
                if (row) row.remove();
            } else {
                alert(data.message || 'Could not delete product.');
                btn.disabled = false;
            }

        } catch (err) {
            alert('Could not connect to the server.');
            btn.disabled = false;
        }
    });
});

function showFieldError(id, message) {
    const el = document.getElementById(id);
    if (el) el.textContent = message;
}

function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; });
}
