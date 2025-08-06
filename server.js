const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const bcrypt = require('bcryptjs');
const session = require('express-session');
const path = require('path');
const bodyParser = require('body-parser');

const app = express();
const PORT = process.env.PORT || 3000;

// Database setup
const db = new sqlite3.Database('./database.db');

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('public'));
app.use(session({
    secret: 'dr-derm-secret-key',
    resave: false,
    saveUninitialized: false,
    cookie: { secure: false }
}));

// View engine
app.set('view engine', 'ejs');
app.set('views', './views');

// Initialize database
db.serialize(() => {
    // Users table
    db.run(`CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        senha TEXT NOT NULL,
        telefone TEXT,
        cpf TEXT,
        conselho_classe TEXT NOT NULL,
        numero_conselho TEXT NOT NULL,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
        ativo BOOLEAN DEFAULT 1
    )`);

    // Categories table
    db.run(`CREATE TABLE IF NOT EXISTS categorias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT,
        ativo BOOLEAN DEFAULT 1
    )`);

    // Products table
    db.run(`CREATE TABLE IF NOT EXISTS produtos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        descricao TEXT,
        marca TEXT,
        modelo TEXT,
        submodelo TEXT,
        lote TEXT,
        vencimento DATE,
        preco REAL NOT NULL,
        preco_original REAL,
        promocao BOOLEAN DEFAULT 0,
        estoque INTEGER DEFAULT 0,
        categoria_id INTEGER,
        imagem TEXT,
        indicacao TEXT,
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
        ativo BOOLEAN DEFAULT 1,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    )`);

    // Insert sample data
    db.run(`INSERT OR IGNORE INTO categorias (id, nome, descricao) VALUES 
        (1, 'Toxina Botulínica', 'Produtos para aplicação de botox'),
        (2, 'Preenchedores', 'Ácido hialurônico e outros preenchedores'),
        (3, 'Agulhas e Seringas', 'Material para aplicação'),
        (4, 'Skincare', 'Produtos para cuidados com a pele')`);

    db.run(`INSERT OR IGNORE INTO produtos (id, nome, marca, modelo, preco, preco_original, promocao, estoque, categoria_id, indicacao, vencimento) VALUES 
        (1, 'Botox 100UI', 'Allergan', 'Botox', 850.00, 950.00, 1, 50, 1, 'Rugas de expressão, hiperidrose', '2025-06-15'),
        (2, 'Dysport 300UI', 'Galderma', 'Dysport', 780.00, NULL, 0, 30, 1, 'Rugas faciais', '2025-08-20'),
        (3, 'Juvederm Ultra', 'Allergan', 'Ultra', 650.00, NULL, 0, 25, 2, 'Preenchimento labial', '2024-12-30'),
        (4, 'Serum Vitamina C', 'SkinCeuticals', 'CE Ferulic', 320.00, 380.00, 1, 15, 4, 'Antienvelhecimento', '2025-03-10')`);
});

// Routes
app.get('/', (req, res) => {
    const queries = [
        'SELECT * FROM produtos WHERE promocao = 1 AND ativo = 1 LIMIT 8',
        `SELECT *, julianday('now') - julianday(vencimento) as dias_vencimento 
         FROM produtos WHERE vencimento <= date('now', '+60 days') AND vencimento > date('now') AND ativo = 1 LIMIT 8`,
        `SELECT * FROM produtos WHERE data_cadastro >= date('now', '-30 days') AND ativo = 1 LIMIT 8`
    ];

    Promise.all(queries.map(query => new Promise((resolve, reject) => {
        db.all(query, (err, rows) => {
            if (err) reject(err);
            else resolve(rows || []);
        });
    }))).then(([promocao, vencimento, novidades]) => {
        res.render('index', { 
            user: req.session.user,
            produtos_promocao: promocao,
            produtos_vencimento: vencimento,
            produtos_novidades: novidades,
            cartCount: req.session.cart ? Object.values(req.session.cart).reduce((a, b) => a + b, 0) : 0
        });
    }).catch(err => {
        console.error(err);
        res.render('index', { 
            user: req.session.user,
            produtos_promocao: [],
            produtos_vencimento: [],
            produtos_novidades: [],
            cartCount: 0
        });
    });
});

app.get('/sobre', (req, res) => {
    res.render('sobre', { 
        user: req.session.user,
        cartCount: req.session.cart ? Object.values(req.session.cart).reduce((a, b) => a + b, 0) : 0
    });
});

app.get('/produtos', (req, res) => {
    const { search, categoria } = req.query;
    let query = 'SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.ativo = 1';
    const params = [];

    if (search) {
        query += ' AND (p.nome LIKE ? OR p.marca LIKE ? OR p.descricao LIKE ?)';
        params.push(`%${search}%`, `%${search}%`, `%${search}%`);
    }
    if (categoria) {
        query += ' AND p.categoria_id = ?';
        params.push(categoria);
    }

    db.all(query, params, (err, produtos) => {
        if (err) {
            console.error(err);
            produtos = [];
        }
        db.all('SELECT * FROM categorias WHERE ativo = 1', (err, categorias) => {
            if (err) {
                console.error(err);
                categorias = [];
            }
            res.render('produtos', { 
                user: req.session.user,
                produtos: produtos || [], 
                categorias: categorias || [], 
                search: search || '', 
                categoria: categoria || '',
                cartCount: req.session.cart ? Object.values(req.session.cart).reduce((a, b) => a + b, 0) : 0
            });
        });
    });
});

app.get('/cadastro', (req, res) => {
    res.render('cadastro', { user: req.session.user, error: null });
});

app.post('/cadastro', async (req, res) => {
    const { nome, email, senha, telefone, cpf, conselho_classe, numero_conselho } = req.body;
    const hashedPassword = await bcrypt.hash(senha, 10);

    db.run(`INSERT INTO usuarios (nome, email, senha, telefone, cpf, conselho_classe, numero_conselho) 
            VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [nome, email, hashedPassword, telefone, cpf, conselho_classe, numero_conselho],
        function(err) {
            if (err) {
                res.render('cadastro', { user: req.session.user, error: 'Erro ao cadastrar usuário' });
            } else {
                res.redirect('/login?success=1');
            }
        });
});

app.get('/login', (req, res) => {
    res.render('login', { user: req.session.user, error: null, success: req.query.success });
});

app.post('/login', (req, res) => {
    const { email, senha } = req.body;

    db.get('SELECT * FROM usuarios WHERE email = ? AND ativo = 1', [email], async (err, user) => {
        if (user && await bcrypt.compare(senha, user.senha)) {
            req.session.user = user;
            res.redirect('/');
        } else {
            res.render('login', { user: null, error: 'E-mail ou senha incorretos', success: null });
        }
    });
});

app.get('/logout', (req, res) => {
    req.session.destroy();
    res.redirect('/');
});

app.get('/carrinho', (req, res) => {
    if (!req.session.cart) {
        return res.render('carrinho', { user: req.session.user, items: [], total: 0, cartCount: 0 });
    }

    const productIds = Object.keys(req.session.cart);
    if (productIds.length === 0) {
        return res.render('carrinho', { user: req.session.user, items: [], total: 0, cartCount: 0 });
    }

    const placeholders = productIds.map(() => '?').join(',');
    db.all(`SELECT * FROM produtos WHERE id IN (${placeholders})`, productIds, (err, produtos) => {
        if (err) {
            console.error(err);
            return res.render('carrinho', { user: req.session.user, items: [], total: 0, cartCount: 0 });
        }
        
        const items = (produtos || []).map(produto => ({
            ...produto,
            quantidade: req.session.cart[produto.id],
            subtotal: produto.preco * req.session.cart[produto.id]
        }));
        const total = items.reduce((sum, item) => sum + item.subtotal, 0);
        
        res.render('carrinho', { 
            user: req.session.user, 
            items, 
            total,
            cartCount: Object.values(req.session.cart).reduce((a, b) => a + b, 0)
        });
    });
});

app.post('/api/cart/add', (req, res) => {
    const { product_id, quantity = 1 } = req.body;
    
    if (!req.session.cart) req.session.cart = {};
    
    req.session.cart[product_id] = (req.session.cart[product_id] || 0) + quantity;
    
    res.json({ success: true, message: 'Produto adicionado ao carrinho' });
});

app.get('/api/cart/count', (req, res) => {
    const count = req.session.cart ? Object.values(req.session.cart).reduce((a, b) => a + b, 0) : 0;
    res.json({ count });
});

app.listen(PORT, () => {
    console.log(`Servidor rodando em http://localhost:${PORT}`);
});