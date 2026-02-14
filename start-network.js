import { networkInterfaces } from 'os';
import { spawn } from 'child_process';

const PORT_LARAVEL = 8000;
const PORT_VITE = 5173;

function getLocalIp() {
  const nets = networkInterfaces();
  for (const name of Object.keys(nets)) {
    for (const net of nets[name] || []) {
      if (net.family === 'IPv4' && !net.internal) return net.address;
    }
  }
  return '127.0.0.1';
}

const ip = getLocalIp();
const env = {
  ...process.env,
  APP_URL: `http://${ip}:${PORT_LARAVEL}`,
  VITE_DEV_SERVER_URL: `http://${ip}:${PORT_VITE}`,
};

console.log(`LAN URL: http://${ip}:${PORT_LARAVEL}`);
console.log('Open this LAN URL in the browser (do not use http://0.0.0.0:8000).');
console.log('If browser blocks Vite assets, use: npm run start:pb (static mode).');

const processes = [];

const run = (cmd, args, options = {}) => {
  const p = spawn(cmd, args, { stdio: 'inherit', env, ...options });
  processes.push(p);

  p.on('exit', () => {
    shutdown(0);
  });

  return p;
};

const shutdown = (code = 0) => {
  for (const p of processes) {
    try {
      p.kill();
    } catch {}
  }
  process.exit(code);
};

process.on('SIGINT', () => shutdown(0));
process.on('SIGTERM', () => shutdown(0));

// Bind Laravel to LAN IP so the displayed URL matches the expected browser origin.
run('php', ['artisan', 'serve', '--host', ip, '--port', `${PORT_LARAVEL}`]);
// Keep Vite reachable from LAN while APP_URL points to the same LAN host.
run('npm', ['run', 'dev', '--', '--host', '0.0.0.0', '--port', `${PORT_VITE}`]);
