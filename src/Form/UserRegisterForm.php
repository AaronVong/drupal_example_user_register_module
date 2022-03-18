<?php

  namespace Drupal\example_user_register\Form;

  use Drupal\Core\Form\FormBase;
  use Drupal\Core\Form\FormStateInterface;
  use Exception;

  class UserRegisterForm extends FormBase {

    public function getFormId() {
      return 'example_user_register_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
      $form['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Họ tên'),
        '#description' => $this->t('Nhập họ và tên'),
        '#required' => TRUE,
        '#weight' => '0',
      ];
      $form['phone'] = [
        '#type' => 'tel',
        '#title' => $this->t('Số ĐT'),
        '#description' => $this->t('Nhập số điện thoại'),
        '#required' => TRUE,
        '#pattern' => '^[0-9]{10,13}$',
        '#weight' => '1',
      ];
      $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Email'),
        '#description' => $this->t('Nhập địa chỉ email thuộc @kyanon.digital'),
        '#pattern' => '^.+@(kyanon\.digital)$',
        '#weight' => '2',
      ];
      $form['age'] = [
        '#type' => 'select',
        '#title' => $this->t('Độ tuổi'),
        '#options' => [
          '10-20' => $this->t('10-20'),
          '20-30' => $this->t('20-30'),
          '30-50' => $this->t('30-50'),
        ],
        '#default_value' => '20-30',
        '#weight' => '3',
      ];
      $form['describe_self'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Mô tả bản thân'),
        '#description' => $this->t('Nhập mô tả về bản thân'),
        '#weight' => '4',
      ];
      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Đăng ký'),
        '#button_type' => 'primary',
        '#weight' => '5',
      ];
      return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('phone')) < 10 || preg_match('/^[0-9]{10,13}$/',$form_state->getValue('phone')) == FALSE) {
      $form_state->setErrorByName('phone', $this->t('Số điện thoại không  hợp lệ'));
    }

    if(strlen($form_state->getValue('name')) <= 0) {
      $form_state->setErrorByName('name', $this->t('Tên không được để trống'));
    }

    if(preg_match("/^.+@(kyanon\.digital)$/",$form_state->getValue('email')) == FALSE) {
      $form_state->setErrorByName('email', $this->t('Email không hợp lệ'));
    }

    if($form_state->getValue('age') === '10-20') {
      $form_state->setErrorByName('age', $this->t('Chưa đủ độ tuổi'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $connection = \Drupal::database();
      $result = $connection->insert('example_user_register')->fields([
        'name' => $form_state->getValue('name'),
        'phone' => $form_state->getValue('phone'),
        'age' => $form_state->getValue('age'),
        'email' => $form_state->getValue('email'),
        'describe' => $form_state->getValue('describe_self'),
      ])->execute();
      $this->messenger()->addStatus($this->t('Thêm người dùng thành công'));
      $this->messenger()->addStatus($this->t('Tên của bạn là @name', ['@name' => $form_state->getValue('name')]));
      $this->messenger()->addStatus($this->t('Số điện thoại của bạn là @phone', ['@phone' => $form_state->getValue('phone')]));
      $this->messenger()->addStatus($this->t('Email của bạn là @email', ['@email' => $form_state->getValue('email')]));
      $this->messenger()->addStatus($this->t('Độ tuổi của bạn là @age', ['@age' => $form_state->getValue('age')]));
      $this->messenger()->addStatus($this->t('Mô tả về bạn là @describe', ['@describe' => $form_state->getValue('describe_self')]));
    } catch(Exception $error) {
      $this->messenger()->addError($this->t("Xẩy ra lỗi " . $error->getMessage()));
    }
  }

}