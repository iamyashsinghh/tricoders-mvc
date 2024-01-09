<?php

class Home extends Controller
{
    protected $user;
    protected $global;
    protected $validationFactory;

    public function __construct()
    {
        $this->user = $this->model('User');
        $this->model('Nav_data');
        $this->model('Admin_nav_data');
        $this->model('Settings');
        $this->model('Msg');
        $this->model('Books');
        $this->model('ABook');
        $this->global = $this->loadConfig('access');
    }

    public function index()
    {
        http_response_code(403);
        echo json_encode(['message' => 'Request Denied']);
    }

    public function nav_data($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $nav_data = Nav_data::all();
            $decodedData = json_decode($nav_data, true);
            foreach ($decodedData as &$record) {
                $record['dropdown_data'] = json_decode($record['dropdown_data'], true);
            }
            echo json_encode($decodedData);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function admin_nav_data($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $admin_nav_data = Admin_nav_data::all();
            echo $admin_nav_data;
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function settings($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $settings = Settings::all();
            echo json_encode($settings);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function insert_nav_data($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }
            $name = $data['name'];
            $link = $data['link'];
            $icon = $data['icon'];
            $dropdown = $data['selectedOptionDrop'];
            $dropdown_data = $data['rows'];
            $newNavData = Nav_data::create([
                'name' => $name,
                'link' => $link,
                'icon' => $icon,
                'dropdown' => $dropdown,
                'dropdown_data' => json_encode($dropdown_data),
            ]);
            if ($newNavData) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Data received successfully.']);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function users_create($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }
            $validationErrors = [];
            if (empty($data['fname']) || strlen($data['fname']) > 255) {
                $validationErrors['fname'] = ['Invalid fname'];
            }
            if (empty($data['lname']) || strlen($data['lname']) > 255) {
                $validationErrors['lname'] = ['Invalid lname'];
            }
            $userWithEmail = User::where('email', $data['email'])->first();
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $validationErrors['email'] = ['Invalid email'];
            } elseif ($userWithEmail) {
                $validationErrors['email'] = ['Email Alredy Exist'];
            }
            if (empty($data['contact'])) {
                $validationErrors['contact'] = ['Invalid contact'];
            }
            $userWithUsername = User::where('username', $data['username'])->first();
            if (empty($data['username'])) {
                $validationErrors['username'] = ['Invalid username'];
            } elseif ($userWithUsername) {
                $validationErrors['username'] = ['Username Alredy Taken'];
            }
            if (empty($data['password']) || strlen($data['password']) < 6) {
                $validationErrors['password'] = ['Invalid password'];
            }
            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                return;
            }

            $fname = $data['fname'];
            $lname = $data['lname'];
            $email = $data['email'];
            $contact = $data['contact'];
            $username = $data['username'];
            $pass = $data['password'];

            $newUser = User::create([
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'username' => $username,
                'contact' => $contact,
                'password' => md5($pass),
            ]);
            if ($newUser) {
                http_response_code(201);
                echo json_encode(['message' => 'Data received successfully.']);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function user_edit($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }

            $fname = $data['fname'];
            $lname = $data['lname'];
            $email = $data['email'];
            $contact = $data['contact'];
            $username = $data['username'];
            $id = $data['id'];
            $mem = $data['mem'];
            $user = User::find($id);

            $validationErrors = [];
            if (empty($data['fname']) || strlen($data['fname']) > 255) {
                $validationErrors['fname'] = ['Invalid fname'];
            }
            if (empty($data['lname']) || strlen($data['lname']) > 255) {
                $validationErrors['lname'] = ['Invalid lname'];
            }

            if ($user->email != $email) {
                $userWithEmail = User::where('email', $data['email'])->first();

                if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $validationErrors['email'] = ['Invalid email'];
                } elseif ($userWithEmail) {
                    $validationErrors['email'] = ['Email Alredy Exist'];
                }
            }
            if (empty($data['contact'])) {
                $validationErrors['contact'] = ['Invalid contact'];
            }
            if ($user->username != $username) {
                $userWithUsername = User::where('username', $data['username'])->first();
                if (empty($data['username'])) {
                    $validationErrors['username'] = ['Invalid username'];
                } elseif ($userWithUsername) {
                    $validationErrors['username'] = ['Username Alredy Taken'];
                }
            }

            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                return;
            }

            if ($user) {
                $user->fname = $fname;
                $user->lname = $lname;
                $user->email = $email;
                $user->username = $username;
                $user->contact = $contact;
                $user->member = $mem;

                if ($user->save()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Data received successfully.']);
                } else {
                    echo json_encode(['message' => 'Something went wrong!']);
                }
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function active_deactive($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }
            $isactive = $data['isactive'];
            $id = $data['id'];
            $user = User::find($id);

            if ($user) {
                $user->isactive = $isactive;
                if ($user->save()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Data received successfully.']);
                } else {
                    echo json_encode(['message' => 'Something went wrong!']);
                }
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function users_login($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }
            $validationErrors = [];

            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $validationErrors['email'] = ['Invalid email'];
            }

            if (empty($data['password']) || strlen($data['password']) < 6) {
                $validationErrors['password'] = ['Invalid password'];
            }

            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                return;
            }

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                $validationErrors['email'] = ['Email not found'];
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                return;
            }

            if ($user->password !== md5($data['password'])) {
                $validationErrors['password'] = ['Invalid password'];
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                return;
            }

            $userData = [
                'id' => $user->id,
                'fname' => $user->fname,
                'lname' => $user->lname,
                'email' => $user->email,
                'username' => $user->username,
                'contact' => $user->contact,
                'member' => $user->member,
            ];
            http_response_code(200);
            echo json_encode(['message' => 'Hello ' . $user->fname . ' login successful', 'data' => $userData]);
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function contact_us($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);

            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }

            $name = $data['name'];
            $email = $data['email'];
            $msg = $data['msg'];

            $newMsg = Msg::create([
                'name' => $name,
                'email' => $email,
                'msg' => $msg,
            ]);

            if ($newMsg) {
                http_response_code(201);
                echo json_encode(['message' => 'Data received successfully.']);
                $this->yash_mailer($email, 'Yash Angular project --- Rest Api', 'Hello ' . $name . ' We have recived your query we will reply soon');
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function admin_user_get($key = '')
    {
        if ($this->global['api_key'] == $key) {

            $newMsg = User::all();

            if ($newMsg) {
                http_response_code(201);
                echo json_encode($newMsg);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function booksa_get($key = '')
    {
        if ($this->global['api_key'] == $key) {

            $AllA = ABook::all();

            if ($AllA) {
                http_response_code(201);
                echo json_encode($AllA);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function books_get($key = '')
    {
        if ($this->global['api_key'] == $key) {

            $allbooks = Books::all();

            if ($allbooks) {
                http_response_code(201);
                echo json_encode($allbooks);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function books_delete($key = '', $id = '')
    {
        if ($this->global['api_key'] == $key) {

            $allbooks = Books::find($id)->delete();

            if ($allbooks) {
                http_response_code(201);
                echo json_encode(['message' => 'Data received successfully.']);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function book_add($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }
            $validationErrors = [];
            if (empty($data['name']) || strlen($data['name']) > 255) {
                $validationErrors['name'] = ['Invalid name'];
            }
            if (empty($data['cost']) || !is_numeric($data['cost']) || $data['cost'] < 0 || $data['cost'] > 255) {
                $validationErrors['cost'] = ['Invalid cost'];
            }
            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                die;
            }

            $name = $data['name'];
            $cost = $data['cost'];

            $newBook = Books::create([
                'name' => $name,
                'cost' => $cost,
            ]);
            if ($newBook) {
                http_response_code(201);
                echo json_encode(['message' => 'Data received successfully.']);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function books_edit($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }

            $name = $data['name'];
            $cost = $data['cost'];
            $id = $data['id'];
            $book = Books::find($id);

            $validationErrors = [];

            if (empty($data['name']) || strlen($data['name']) > 255) {
                $validationErrors['name'] = ['Invalid name'];
            }
            if (empty($data['cost']) || !is_numeric($data['cost']) || $data['cost'] < 0 || $data['cost'] > 255) {
                $validationErrors['cost'] = ['Invalid cost'];
            }

            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode(['message' => 'Validation failed', 'errors' => $validationErrors]);
                return;
            }

            if ($book) {
                $book->name = $name;
                $book->cost = $cost;
                if ($book->save()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Data received successfully.']);
                } else {
                    echo json_encode(['message' => 'Something went wrong!']);
                }
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function dash_get($key = '')
    {
        if ($this->global['api_key'] == $key) {

            $totalbooks = Books::all()->count();
            $totalUsers = User::all()->count();
            $totalMsg = Msg::all()->count();
            $totalABook = ABook::all()->count();
            $dash_data = [];
            $dash_data['totalbooks'] = $totalbooks;
            $dash_data['totalUsers'] = $totalUsers;
            $dash_data['totalMsg'] = $totalMsg;
            $dash_data['totalABook'] = $totalABook;

            if ($dash_data) {
                http_response_code(201);
                echo json_encode($dash_data);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }

    public function booka_add($key = '')
    {
        if ($this->global['api_key'] == $key) {
            $data = json_decode(file_get_contents("php://input"), true);
            if ($data === null) {
                echo json_encode(['message' => 'Invalid JSON data']);
                return;
            }

            $user_id = $data['user_id'];
            $book_id = $data['book_id'];

            $newBookA = ABook::create([
                'users_id' => $user_id,
                'book_id' => $book_id,
            ]);

            if ($newBookA) {
                http_response_code(201);
                echo json_encode(['message' => 'Data received successfully.']);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }

        } else {
            echo json_encode(['message' => 'Invalid Api Key']);
        }
    }
    public function booka_update($key = '')
{
    if ($this->global['api_key'] == $key) {
        $data = json_decode(file_get_contents("php://input"));
        if ($data === null) {
            echo json_encode(['message' => 'Invalid JSON data']);
            return;
        }

        $book_id = $data->book_id; 
        $user_id = $data->user_id; 
        $id = $data->id; 
        $booka = ABook::find($id);

        if ($booka) {
            $booka->users_id = $user_id; 
            $booka->book_id = $book_id; 
            if ($booka->save()) {
                http_response_code(201);
                echo json_encode(['message' => 'Data received successfully.']);
            } else {
                echo json_encode(['message' => 'Something went wrong!']);
            }
        } else {
            echo json_encode(['message' => 'Something went wrong!']);
        }
    } else {
        echo json_encode(['message' => 'Invalid Api Key']);
    }
}



}
