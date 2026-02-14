import { networkInterfaces } from 'os';
import { spawn } from 'child_process';

const PORT_LARAVEL = 8000;

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
};

console.log(`LAN URL (static assets): http://${ip}:${PORT_LARAVEL}`);
console.log('Building assets for network-safe mode...');

const build = spawn('npm', ['run', 'build'], { stdio: 'inherit', env });
let server = null;

const shutdown = (code = 0) => {
  if (server) {
    try {
      server.kill();
    } catch {}
  }
  process.exit(code);
};

build.on('exit', (code) => {
  if (code !== 0) {
    process.exit(code);
    return;
  }

  console.log(`Starting Laravel server on http://${ip}:${PORT_LARAVEL}`);
  server = spawn('php', ['artisan', 'serve', '--host', ip, '--port', `${PORT_LARAVEL}`], {
    stdio: 'inherit',
    env,
  });

  server.on('exit', (serverCode) => shutdown(serverCode ?? 0));
});

process.on('SIGINT', () => shutdown(0));
process.on('SIGTERM', () => shutdown(0));
