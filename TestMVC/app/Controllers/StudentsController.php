<?php

class StudentsController extends Controller
{
    private $studentModel;

    public function __construct()
    {
        $this->studentModel = new Student();
    }

    public function index()
    {
        $students = $this->studentModel->all();

        return $this->view('students/index', [
            'title' => 'Danh sach sinh vien',
            'students' => $students,
        ]);
    }

    public function create()
    {
        return $this->view('students/create', [
            'title' => 'Them sinh vien',
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store()
    {
        $data = $this->studentData();
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->view('students/create', [
                'title' => 'Them sinh vien',
                'errors' => $errors,
                'old' => $data,
            ]);
        }

        try {
            $this->studentModel->create($data);
            flash('success', 'Da them sinh vien moi.');
            redirect('students');
        } catch (PDOException $e) {
            return $this->view('students/create', [
                'title' => 'Them sinh vien',
                'errors' => ['email' => 'Email da ton tai hoac du lieu khong hop le.'],
                'old' => $data,
            ]);
        }
    }

    public function edit($id)
    {
        $student = $this->studentModel->find($id);

        if (!$student) {
            return $this->notFound('Khong tim thay sinh vien.');
        }

        return $this->view('students/edit', [
            'title' => 'Cap nhat sinh vien',
            'student' => $student,
            'errors' => [],
        ]);
    }

    public function update($id)
    {
        $student = $this->studentModel->find($id);

        if (!$student) {
            return $this->notFound('Khong tim thay sinh vien.');
        }

        $data = $this->studentData();
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->view('students/edit', [
                'title' => 'Cap nhat sinh vien',
                'student' => array_merge($student, $data),
                'errors' => $errors,
            ]);
        }

        try {
            $this->studentModel->update($id, $data);
            flash('success', 'Da cap nhat sinh vien.');
            redirect('students');
        } catch (PDOException $e) {
            return $this->view('students/edit', [
                'title' => 'Cap nhat sinh vien',
                'student' => array_merge($student, $data),
                'errors' => ['email' => 'Email da ton tai hoac du lieu khong hop le.'],
            ]);
        }
    }

    public function delete($id)
    {
        $this->studentModel->delete($id);

        flash('success', 'Da xoa sinh vien.');
        redirect('students');
    }

    private function studentData()
    {
        return [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'major' => trim($_POST['major'] ?? ''),
        ];
    }

    private function validate($data)
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Vui long nhap ho ten.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Vui long nhap email.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email khong dung dinh dang.';
        }

        if ($data['major'] === '') {
            $errors['major'] = 'Vui long nhap chuyen nganh.';
        }

        return $errors;
    }
}

