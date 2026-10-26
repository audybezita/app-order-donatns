document.addEventListener('click', function (event) {
  const btn = event.target.closest('.qty-btn');
  if (!btn) return;

  const productId = btn.dataset.product;
  const input = document.querySelector('.qty-input[data-product="' + productId + '"]');
  if (!input) return;

  let value = Number(input.value || 1);
  if (btn.dataset.action === 'plus') value += 1;
  if (btn.dataset.action === 'minus') value = value > 1 ? value - 1 : 1;
  input.value = value;
});
