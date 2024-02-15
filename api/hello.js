// api/hello.js

import { readFileSync, writeFileSync } from 'fs';
import path from 'path';

export default function handler(req, res) {
  const file = path.join(process.cwd(), 'files', 'test.json');
  let data;

  try {
    data = JSON.parse(readFileSync(file, 'utf8'));
  } catch (error) {
    data = {};
  }

  const deviceId = req.cookies.device_id || 'unique-id';
  const parametro = req.query.request || '';

  if (!data[deviceId]) {
    // Adiciona novo dispositivo com parâmetro
    data[deviceId] = { last_request: Date.now(), parametro };
  } else {
    // Atualiza a data da última solicitação e o parâmetro se já existe
    data[deviceId].last_request = Date.now();
    data[deviceId].parametro = parametro;
  }

  // Remove dispositivos inativos após 30s
  for (const id in data) {
    if (Date.now() - data[id].last_request > 30000) {
      delete data[id];
    }
  }

  // Salva os dados de volta no arquivo JSON
  writeFileSync(file, JSON.stringify(data));

  res.setHeader('Content-Type', 'application/json');
  return res.end(JSON.stringify({ parametro, dispositivos: countDevices(data, parametro) }));
}

function countDevices(data, parametro) {
  return Object.values(data).filter(device => device.parametro === parametro).length;
}
