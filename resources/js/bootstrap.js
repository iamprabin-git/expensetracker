import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Dropdown from 'bootstrap/js/dist/dropdown.js';
import Modal from 'bootstrap/js/dist/modal.js';

window.bootstrap = { Dropdown, Modal };
