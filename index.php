<?php
// === Visitor Logging ===
$logFile = __DIR__ . "/visitors.log"; // file to store visits
$now = time();

// Load existing log
$visits = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES) : [];

// Keep only visits from the last 7 days
$visits = array_filter($visits, fn($v) => (int)$v >= $now - 7 * 24 * 60 * 60);

// Add this visit (timestamp)
$visits[] = $now;

// Save updated log
file_put_contents($logFile, implode(PHP_EOL, $visits));

// Count total visits in the last 7 days
$totalVisitors = count($visits);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Smart Contract Storage Explorer | Ethereum, BSC, Polygon, Arbitrum</title>
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/favicon.ico"
  <!-- SEO Meta Tags -->
  <meta name="description" content="Explore and analyze Ethereum, BSC, Polygon, Arbitrum, Avalanche, Fantom, Cronos, and Moonbeam smart contract storage slots. A free blockchain storage explorer tool.">
  <meta name="keywords" content="smart contract storage, Ethereum storage explorer, blockchain explorer, smart contract slots, EVM storage, Polygon, BSC, Arbitrum, Avalanche, Fantom, Cronos, Moonbeam, ethers.js tool">
  <meta name="author" content="StorageXplore">

  <!-- Open Graph -->
  <meta property="og:title" content="Smart Contract Storage Explorer">
  <meta property="og:description" content="Free online tool to view and analyze smart contract storage slots across Ethereum, BSC, Polygon, Arbitrum, Avalanche, Fantom, Cronos, and Moonbeam.">
  <meta property="og:url" content="https://storagexplore.top/">
  <meta property="og:type" content="website">
  <meta property="og:image" content="https://storagexplore.top/preview.png">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Smart Contract Storage Explorer">
  <meta name="twitter:description" content="View blockchain smart contract storage slots on Ethereum, BSC, Polygon, and more.">
  <meta name="twitter:image" content="https://storagexplore.top/preview.png">

  <!-- Canonical -->
  <link rel="canonical" href="https://storagexplore.top/">

  <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    h1 {
      text-align: center;
      color: #4a6fa5;
      margin-bottom: 10px;
      font-size: 22px;
    }
    .description {
      text-align: center;
      margin-bottom: 10px;
      color: #666;
    }
    .visitor-stats {
      text-align: center;
      font-size: 13px;
      color: #555;
      margin-bottom: 20px;
    }
    .card {
      background: #fff;
      max-width: 900px;
      margin: 0 auto 20px auto;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: 600; }
    select, input {
      width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;
    }
    button {
      background: #4a6fa5; color: #fff; border: none;
      padding: 10px 16px; border-radius: 4px;
      font-weight: 600; cursor: pointer;
    }
    button:disabled { background: #bbb; cursor: not-allowed; }
    .loading { text-align: center; margin: 10px 0; }
    .fetching-address {
      text-align: center;
      font-size: 13px;
      margin: 5px 0 10px 0;
      color: #444;
      font-style: italic;
    }
    .error-message {
      color: #dc3545; background: #f8d7da;
      padding: 10px; border-radius: 4px;
      margin-top: 10px; display: none;
    }
    .stats {
      text-align: center; font-size: 14px; margin-bottom: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }
    th, td {
      border: 1px solid #e1e4e8;
      padding: 6px 8px;
      text-align: left;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 250px;
    }
    th {
      background: #f0f2f5;
      font-weight: 600;
      position: sticky;
      top: 0;
      z-index: 2;
    }
    tbody tr:nth-child(even) { background: #fafafa; }
    .value-cell { font-family: monospace; }
    .small-select {
      font-size: 12px; padding: 3px;
    }
    .support-box {
      position: fixed;
      top: 10px;
      right: 10px;
      background: #fff;
      border: 1px solid #ddd;
      padding: 8px 12px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      font-size: 12px;
      color: #333;
    }
    .support-box strong {
      display: block;
      margin-bottom: 4px;
      font-size: 13px;
      color: #4a6fa5;
    }
    .support-box code {
      font-family: monospace;
      font-size: 11px;
      word-break: break-all;
    }
    /* hidden SEO keywords */
    .seo-hidden {
      position: absolute;
      left: -9999px;
      height: 1px;
      width: 1px;
      overflow: hidden;
    }
  </style>
</head>
<body>
  <!-- Support box -->
  <div class="support-box">
    <strong>Support the project</strong>
    <div>Send ETH or Tokens to:</div>
    <code>0x73A73E887f9A65Af28B1188f0468f5fB631f96CC</code>
  </div>

  <h1>Smart Contract Storage Explorer</h1>
  <p class="description">
    View the storage slots of a smart contract
  </p>

  <!-- Visitor stats -->
  <div class="visitor-stats">
    Total visitors in the last 7 days: <strong><?php echo $totalVisitors; ?></strong>
  </div>

  <!-- Hidden SEO Keywords -->
  <div class="seo-hidden">
    Smart contract storage explorer for Ethereum, Binance Smart Chain (BSC), Polygon, Arbitrum, Avalanche, Fantom, Cronos, and Moonbeam. 
    Free blockchain tool for EVM developers, auditors, and researchers to read and analyze storage slots in real time using ethers.js. 
    Explore DeFi, NFT, and dApp contract data directly from blockchain storage. 
    Keywords: Ethereum storage, BSC storage explorer, Polygon contract data, smart contract slots, EVM storage viewer, blockchain debugging tool, on-chain analysis.
  </div>

  <!-- Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "StorageXplore",
    "url": "https://storagexplore.top",
    "description": "Smart Contract Storage Explorer for Ethereum, BSC, Polygon, Arbitrum, Avalanche, Fantom, Cronos, and Moonbeam.",
    "applicationCategory": "Blockchain Tool",
    "operatingSystem": "All",
    "keywords": "Ethereum, smart contract storage, blockchain explorer, BSC, Polygon, Arbitrum, Avalanche, Fantom, Cronos, Moonbeam"
  }
  </script>

  <!-- Input form wrapper -->
  <div class="card" id="form-container">
    <div class="form-group">
      <label for="rpc-select">RPC Endpoint</label>
      <select id="rpc-select">
        <option value="https://ethereum-rpc.publicnode.com">Ethereum</option>
        <option value="https://bsc-dataseed.binance.org">BSC</option>
        <option value="https://polygon-rpc.com">Polygon</option>
        <option value="https://arb1.arbitrum.io/rpc">Arbitrum</option>
        <option value="https://api.avax.network/ext/bc/C/rpc">Avalanche</option>
        <option value="https://rpc.ftm.tools">Fantom</option>
        <option value="https://evm.cronos.org">Cronos</option>
        <option value="https://rpc.api.moonbeam.network">Moonbeam</option>
      </select>
    </div>

    <div class="form-group">
      <label for="contract-address">Contract Address</label>
      <input type="text" id="contract-address" placeholder="0x..." />
    </div>

    <div class="form-group">
      <label for="slot-count">Number of Storage Slots</label>
      <input type="number" id="slot-count" value="300" min="1" max="10000" />
    </div>

    <button id="fetch-button">Fetch Storage Slots</button>
    <div id="error-message" class="error-message"></div>
  </div>

  <div class="card" id="results-container" style="display: none;">
    <div class="fetching-address" id="fetching-address"></div>
    <div class="stats">
      <span id="stats-text"></span>
      <label style="margin-left:10px;">
        <input type="checkbox" id="hide-zeros" checked /> Hide zero-value slots
      </label>
    </div>
    <div class="loading" id="loading">Fetching storage slots...</div>
    <table>
      <thead>
        <tr>
          <th>Slot</th>
          <th>Value</th>
          <th>Format</th>
        </tr>
      </thead>
      <tbody id="results"></tbody>
    </table>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const rpcSelect = document.getElementById('rpc-select');
      const contractInput = document.getElementById('contract-address');
      const slotInput = document.getElementById('slot-count');
      const fetchButton = document.getElementById('fetch-button');
      const formContainer = document.getElementById('form-container');
      const resultsContainer = document.getElementById('results-container');
      const resultsTable = document.getElementById('results');
      const statsText = document.getElementById('stats-text');
      const loadingEl = document.getElementById('loading');
      const errorEl = document.getElementById('error-message');
      const hideZerosCheckbox = document.getElementById('hide-zeros');
      const fetchingAddressEl = document.getElementById('fetching-address');

      let storageSlots = [];
      let provider = null;

      if (typeof ethers === 'undefined') {
        showError('Ethers.js failed to load.');
        fetchButton.disabled = true;
        return;
      }

      fetchButton.addEventListener('click', fetchStorageSlots);
      contractInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') fetchStorageSlots();
      });
      hideZerosCheckbox.addEventListener('change', renderSlots);

      async function fetchStorageSlots() {
        const rpcUrl = rpcSelect.value;
        const addr = contractInput.value.trim();
        const slotCount = parseInt(slotInput.value) || 300;

        if (!/^0x[a-fA-F0-9]{40}$/.test(addr)) {
          showError('Please enter a valid contract address');
          return;
        }

        storageSlots = [];
        resultsTable.innerHTML = '';
        hideError();
        resultsContainer.style.display = 'block';
        formContainer.style.display = 'none';
        loadingEl.style.display = 'block';
        fetchingAddressEl.textContent = `Currently fetching contract ${addr}`;
        fetchButton.disabled = true;
        fetchButton.textContent = 'Fetching...';

        try {
          provider = new ethers.providers.JsonRpcProvider(rpcUrl);
          await provider.getNetwork();

          const batchSize = 25;
          for (let start = 0; start < slotCount; start += batchSize) {
            const batch = [];
            for (let i = start; i < start + batchSize && i < slotCount; i++) {
              batch.push(
                provider.getStorageAt(addr, i)
                  .then(val => ({
                    slot: i,
                    value: val,
                    isZero: val === ethers.constants.HashZero
                  }))
                  .catch(() => ({
                    slot: i,
                    value: 'Error',
                    isZero: false,
                    error: true
                  }))
              );
            }
            const results = await Promise.all(batch);
            storageSlots.push(...results);
            appendRows(results);
            updateStats();
          }
        } catch (err) {
          showError(err.message);
          formContainer.style.display = 'block';
          fetchingAddressEl.textContent = '';
        } finally {
          loadingEl.style.display = 'none';
          fetchButton.disabled = false;
          fetchButton.textContent = 'Fetch Storage Slots';
        }
      }

      function appendRows(slots) {
        const hideZeros = hideZerosCheckbox.checked;
        slots.forEach(slot => {
          if (hideZeros && slot.isZero) return;
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${slot.slot}</td>
            <td class="value-cell" id="value-${slot.slot}">${formatValue(slot.value,'hex')}</td>
            <td>
              <select class="small-select" id="format-${slot.slot}"> 
                <option value="hex">Hex</option>
                <option value="number">Number</option>
                <option value="text">Text</option>
                <option value="address">Address</option>
                <option value="bytes">Bytes</option>
              </select>
            </td>`;
          resultsTable.appendChild(row);

          document
            .getElementById(`format-${slot.slot}`)
            .addEventListener("change", e => {
              const type = e.target.value;
              document.getElementById(`value-${slot.slot}`).innerHTML =
                formatValue(slot.value, type);
            });
        });
      }

      function updateStats() {
        const total = storageSlots.length;
        const zeroCount = storageSlots.filter(s=>s.isZero).length;
        const nonZero = total - zeroCount;
        const shown = hideZerosCheckbox.checked ? nonZero : total;
        statsText.textContent = `Showing ${shown} of ${total} slots (${zeroCount} zero, ${nonZero} non-zero)`;
      }

      function renderSlots() {
        resultsTable.innerHTML = '';
        appendRows(storageSlots);
        updateStats();
      }

      function formatValue(value, type) {
        if (value === 'Error') return `<span style="color:#dc3545">Error</span>`;
        try {
          switch(type) {
            case 'hex': return value;
            case 'number': return ethers.BigNumber.from(value).toString();
            case 'text':
              try {
                const hex = value.substring(2).replace(/0+$/,'');
                return hex.length%2===0 ? ethers.utils.toUtf8String('0x'+hex) : 'Invalid UTF-8';
              } catch { return 'Invalid UTF-8'; }
            case 'address':
              if (value.length===66) {
                const addr='0x'+value.slice(-40);
                return ethers.utils.isAddress(addr)?addr:'Not an address';
              }
              return 'Not an address';
            case 'bytes':
              try {
                const bytes = ethers.utils.arrayify(value);
                return Array.from(bytes.filter(b=>b!==0)).join(',');
              } catch { return 'Error'; }
            default: return value;
          }
        } catch { return 'Error'; }
      }

      function showError(msg){ errorEl.textContent=msg; errorEl.style.display='block'; }
      function hideError(){ errorEl.style.display='none'; }
    });
  </script>
  <!-- Social footer -->
  <footer style="margin-top:40px; padding:15px; text-align:center; font-size:14px; color:#555;">
    <a href="https://x.com/0x21SAFE" target="_blank"
       style="display:inline-block; margin:0 10px; text-decoration:none; font-weight:600; padding:6px 12px; border-radius:6px; background:#1DA1F2; color:#fff;">
       Twitter
    </a>
    <a href="https://github.com/SeifElsallamy/storagexplore" target="_blank"
       style="display:inline-block; margin:0 10px; text-decoration:none; font-weight:600; padding:6px 12px; border-radius:6px; background:#333; color:#fff;">
       GitHub
    </a>
  </footer>
</body>
</html>
