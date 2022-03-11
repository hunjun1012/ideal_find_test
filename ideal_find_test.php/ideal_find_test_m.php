<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/Common.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/StringUtil.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/DBUtil.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/FileUtil.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/Logger.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/sms/smsProc.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/FrontCommon.php");
$counselGbn = getParam("counselGbn", "28404");
?>
<!DOCTYPE html>
<html class="no-js" lang="ko">

<head>
  <?
  require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/meta_k.php");
  ?>
  <!-- 웹으로 모바일 페이지 보면 리다이렉트 -->
  <script>
    if ($(window).width() > 780) {
      $.getScript('js/nbw-parallax.js');
    }
    if (window.innerWidth > 780) {
      window.location.href = 'https://www.isoohyun.co.kr/new/lovetest/ideal_find_test.php?counselGbn=<?= $counselGbn ?>&mctkey=<?= $mctkey ?>';
    }
  </script>
  <!-- // progressbar animate bootstrap 20220304 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <!-- // progressbar animate bootstrap 20220304 -->
  <script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
  <script>
    Kakao.init('16b3c92425889edb797d2dc78b3d1428'); // 발급받은 키 중 javascript키를 사용해준다.
    //카카오 정보 가져오기
    function kakaoGetData() {
      Kakao.Auth.login({
        success: function(response) {
          console.log(response);
          Kakao.API.request({
            url: '/v2/user/me',
            success: function(response) {
              var user_id = "k_" + response.id; // 아이디
              var birthyear = response.kakao_account.birthyear; // 생일
              var email = response.kakao_account.email; // 이메일
              var gender = response.kakao_account.gender; // 성별
              if (gender == 'male') { // DB에 맞는 성별처리
                gender = '1';
              } else {
                gender = '2';
              }
              var phone_number = response.kakao_account.phone_number; // 핸드폰번호
              var phone_number = phone_number.replace('+82 ', '0'); // 핸드폰 앞자리 치환
              var nickname = response.properties.nickname; // 카카오톡 닉네임

              $('#user_id').val(user_id);
              $('#birthday').val(birthyear);
              $('#email').val(email);
              $('#gender').val(gender);
              $('#phone').val(phone_number);
              $('#name').val(nickname);

            },
            fail: function(error) {
              console.log(error)
            },
          })
          Kakao.API.request({
            url: '/v1/user/shipping_address',
            success: function(response) { // 우선 첫번째 등록한 주소를 불러오도록...
              var base_address = response.shipping_addresses[0].base_address;
              var detail_address = response.shipping_addresses[0].detail_address;
              var zone_number = response.shipping_addresses[0].zone_number;
              $('#area').val(base_address);
              $('#area_post_number').val(zone_number); //신주소 우편번호
              //$('#detail_address').val(detail_address);
              //$('#zone_number').val(zone_number);
            },
            fail: function(error) {
              console.log(error)
            },
          })
          // 카카오 정보 및 form 정보 넘기는 부분
          setTimeout(function() {
            $('#frm').validate({
              success: function() {
                var content = '';
                content += ' [희망연령] : ' + sage;
                content += ' [희망신장] : ' + scm;
                content += ' [희망학력] : ' + sschool;
                content += ' [희망직업] : ' + sjob;
                $(this).value('content', content);

                // 추천이상형 프로필 등록할때 들어가는 데이터
                // $('#Idealtype_age').val($('input[name=sage]').val().substring(1));
                // $('#Idealtype_height').val($('input[name=scm]').val().substring(1));
                // $('#Idealtype_school').val($('input[name=sschool]').val().substring(1));
                // $('#Idealtype_job').val($('input[name=sjob]').val().substring(1));

                this.target = "counselResult";
                this.action = "/new/common/counselProck.php";
                this.submit();

                // alert("신청되었습니다.");
              }
            })
          }, 1000);
        },
        fail: function(error) {
          console.log(error)
        },
      })
    }

    // 이상형 전역변수
    // var qgender = '';
    // var qage = '';
    // var qschool = '';
    // var qpay = '';
    // var qjob = '';
    // var qlook = '';

    // ideal type
    var sage = '';
    var scm = '';
    var sschool = '';
    var sjob = '';

    //페이지 열릴 때 show(0)으로 이동
    $(document).ready(function() {
      show(0);
    });

    // show()함수
    function show(idx, txt) {
      if (idx == 2) {
        if ($('#school').val() == "") {
          alert("학력을 선택해주세요");
          return false;
        } else if ($('select[name=new_birthday]').val() == "") {
          alert('출생년도를 선택해주세요.');
          $('select[name=new_birthday]').focus();
          return;
        }
        $(".progress-bar").animate({
          width: "25%",
        }, 1000);
        $(".progress-bar2").animate({
          paddingLeft: '65px',
        }, 1000);
      } else if (idx == 3) { //이상형 나이
        sage = txt;
        console.log(sage);
        $(".progress-bar").animate({
          width: "50%",
        }, 1000);
        $(".progress-bar2").animate({
          paddingLeft: '130px',
        }, 1000);
      } else if (idx == 4) { // 이상형 키
        scm = txt;
        console.log(scm);
        $(".progress-bar").animate({
          width: "75%",
        }, 1000);
        $(".progress-bar2").animate({
          paddingLeft: '195px',
        }, 1000);
      } else if (idx == 5) { // 이상형 학력
        sschool = txt;
        console.log(sschool);
        $(".progress-bar").animate({
          width: "100%",
        }, 1000);
        $(".progress-bar2").animate({
          paddingLeft: '255px',
        }, 1000);
      } else if (idx == 6) { // 이상형 직업
        sjob = txt;
        console.log(sjob);
        // content = (' [희망연령] : ' + sage + ' [희망신장] : ' + scm + ' [희망학력] : ' + sschool + ' [희망직업] : ' + sjob);
        // console.log(content);
      }

      $('section').hide();
      $('section:eq(' + idx + ')').show();


    }

    function success() {
      $('#frm').get(0).reset();
      show(7);
    }

    // select시 색 변경
    function changecolor1() {
      $("#new_birthday").css("background-color", "#ff83bd");
      $("#new_birthday").css("border", "1px solid white");
      $("#new_birthday").css("color", "white");
      console.log("change1");
    }

    function changecolor2() {
      $("#school").css("background-color", "#ff83bd");
      $("#school").css("border", "1px solid white");
      $("#school").css("color", "white");
      console.log("change2");
    }

    // progressbar animate bootstrap 20220304
    var delay = 500;
    $(".progress-bar").each(function(i) {
      $(this).delay(delay * i).animate({
        width: $(this).attr('aria-valuenow') + '%'
      }, delay);

      $(this).prop('Counter', 0).animate({
        Counter: $(this).text()
      }, {
        duration: delay,
        // easing: 'swing',
        step: function(now) {
          $(this).text(Math.ceil(now) + '%');
        }
      });
    });
  </script>
  <!-- p text -->
  <style>
    /* // progressbar animate bootstrap 20220304 */
    .progress {
      margin-bottom: 20px;
    }

    .progress-bar {
      width: 0;
    }

    .bg-purple {
      background-color: #825CD6 !important;
    }

    .progress .progress-bar {
      transition: unset;
    }

    h2 {
      margin-bottom: 0;

    }

    /* // progressbar animate bootstrap 20220304 */
    .p_text {
      text-align: center;
      font-size: 20px;
      padding-top: 20px;
      padding-bottom: 20px;
      color: #aa40dc;
    }

    .list_box {
      display: block;
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 5% 4%;
      font-size: 15px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.5;
    }

    .list_box:hover {
      display: block;
      width: 80%;
      border: 3px solid #ff459c;
      text-align: center;
      padding: 5% 4%;
      font-size: 15px;
      color: white;
      background-color: #ff83bd;
      opacity: 0.7;
    }

    .radio-box {
      display: flex;
      width: 300px;
      text-align: center;
    }

    .join-charge {
      background-color: #ffea73;
    }

    input[id="gender1"]+label {
      width: 40%;
      border: 3px solid white;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="gender1"]:checked+label {
      width: 40%;
      border: 3px solid #ff459c;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: white;
      background-color: #ff83bd;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="gender2"]+label {
      width: 40%;
      border: 3px solid white;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="gender2"]:checked+label {
      width: 40%;
      border: 3px solid #ff459c;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: white;
      background-color: #ff83bd;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="marry1"]+label {
      width: 40%;
      border: 3px solid white;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="marry1"]:checked+label {
      width: 40%;
      border: 3px solid #ff459c;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: white;
      background-color: #ff83bd;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="marry2"]+label {
      width: 40%;
      border: 3px solid white;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    input[id="marry2"]:checked+label {
      width: 40%;
      border: 3px solid #ff459c;
      text-align: center;
      padding: 5% 3%;
      font-size: 20px;
      color: white;
      background-color: #ff83bd;
      opacity: 0.7;
      margin-bottom: 10px;
    }

    #new_birthday {
      width: 82%;
      border: 3px solid white;
      text-align: center;
      padding: 10%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.8;
      margin-bottom: 30px;
    }


    #school {
      width: 82%;
      border: 3px solid white;
      text-align: center;
      padding: 10%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.8;
      margin-bottom: 30px;
    }

    select {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      text-indent: 15px;
    }
  </style>
</head>

<body>
  <div class="wrap">
    <?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/header.php");
    ?>
    <div id="container">
      <?
      require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/pageTitle.php");
      ?>
    </div>
    <div id="layer_fixeds" class="phone-links">
      <a class="" href="/nm/common/Counseling.php"><i class=""></i>1:1문의</a>
      <a class="" href="/nm/common/brochure.php"><i class=""></i>브로셔신청</a>
      <a href="tel:025404000"><i class="ico-phone"></i>전화상담</a>
    </div>
    <!-- 시작부분 show(0) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('../../static/images/lovetest/ideal_find_test/m_start2.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <p class="btn" style="height: 26px;"><a href="javascript:show(1);"><img style="width: 83%;" src="../../static/images/lovetest/ideal_find_test/m_start_btn.png" alt="" /></a></p>
        </div>
      </div>
    </section>

    <section id="lovetest">
      <form id="frm" name="frm" method="post">
        <input type="hidden" name="counselGbn" value="<?= getParam("counselGbn", "28404") ?>" />
        <input type="hidden" name="counselGbn2" value="이상형 찾기" />
        <input type="hidden" name="marriage" value="10501" />
        <input type="hidden" id="name" name="name">
        <input type="hidden" id="gender" name="gender">
        <input type="hidden" id="birthday" name="birthday">
        <input type="hidden" id="area" name="area">
        <input type="hidden" id="phone" name="phone">
        <input type="hidden" id="email" name="email">
        <input type="hidden" name="content" />
        <input type="hidden" name="user_id" id="user_id" />
        <input type="hidden" id="area_post_number" name="area_post_number">

        <!-- show(1) -->
        <div class="join-charge">
          <div style="background-image: url('../../static/images/lovetest/ideal_find_test/mobile_bg01.png'); height:600px; background-repeat: no-repeat; background-position:center;">
            <div class="input-box">

              <div style="margin-left:20px; padding-top:25px;">
                <img src="../../static/images/lovetest/ideal_find_test/m_btn_prev.png" alt="" onclick="show(0);return false;" />
              </div>

              <p class="p_text" style="padding-top: 30px;">이상형 매칭을 위해<br>당신의 몇 가지 정보를 입력해주세요</p>
              <div style="text-align: center;  margin:0 auto;">
                <div style="width: 100%;">
                  <input id="gender1" type="radio" name="gender" value="1" style="display: none;" /> <label for="gender1">남성</label>&nbsp;
                  <input id="gender2" type="radio" name="gender" value="2" style="display: none;" /><label for="gender2">여성</label>
                </div>
                <div style="width: 100%;">
                  <input id="marry1" type="radio" name="marriage" value="10501" style="display: none;" /><label for="marry1"> 초혼</label>&nbsp;
                  <input id="marry2" type="radio" name="marriage" value="10502" style="display: none;" /> <label for="marry2">재혼</label>
                </div>
                <div style="width: 100%; padding-bottom:10px;">
                  <select onchange="changecolor1();" id="new_birthday" name="new_birthday" style="height: 50px;">
                    <option value="">출생년도</option>
                    <? for ($i = 1950; $i < date('Y'); $i++) { ?>
                      <option value="<?= $i ?>"><?= $i; ?>년</option>
                    <? } ?>
                  </select>
                </div>
                <div style="width: 100%;">
                  <select onchange="changecolor2();" id="school" name="school" message="학력을 선택해주세요." style="height: 50px;">
                    <option value="">학력</option>
                    <option value="대학(2, 3년제) 재학">대학(2, 3년제) 재학</option>
                    <option value="대학(2, 3년제) 졸업">대학(2, 3년제) 졸업</option>
                    <option value="대학(4년제) 재학">대학(4년제) 재학</option>
                    <option value="대학(4년제) 졸업">대학(4년제) 졸업</option>
                    <option value="대학원(석사) 재학">대학원(석사) 재학</option>
                    <option value="대학원(석사) 졸업">대학원(석사) 졸업</option>
                    <option value="대학원(박사) 재학">대학원(박사) 재학</option>
                    <option value="대학원(박사) 졸업">대학원(박사) 졸업</option>
                    <option value="고등학교 졸업">고등학교 졸업</option>
                    <option value="기타">기타</option>
                  </select>
                </div>
              </div>
              <center>
                <img src="../../static/images/lovetest/ideal_find_test/m_btn_next.png" alt="" style="width: 82%; text-align:center; margin-top:10px;" onclick="show(2);return false;" />
              </center>
            </div>
          </div>
        </div>

      </form>
      <iframe src="" id="counselResult" name="counselResult" width="0" height="0" style="display:none;" frameborder="0"></iframe>

    </section>

    <!-- show(2) -->
    <section id="lovetest">
      <div class="join-charge">
        <div style="background-image: url('../../static/images/lovetest/ideal_find_test/mobile_bg01.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <div style="margin-left:20px; padding-top:25px;">
            <img src="../../static/images/lovetest/ideal_find_test/m_btn_prev.png" alt="" onclick="show(1);return false;" />
          </div>
          <div class="container" style="width: 300px; padding-top:10px;">
            <img class="progress-bar2" src="/new/image/ideal_find_test/heart.png"></img>
            <div class="progress border">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <p class="p_text">Q1. 내가 찾는 이상형의 나이를 <br> 선택해 주세요</p>
          <div style="margin-top: 10px;"></div>
          <center>
            <ul class="radio-box">
              <li class="list_box" onclick="show(3,'24세이하');">24세이하</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(3,'25세~29세');">25세~29세</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(3,'30세~34세');">30세~34세</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(3,'35세~39세');">35세~39세</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(3,'40세~44세');">40세~44세</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(3,'45세~49세');">45세~49세</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(3,'50세~55세');">50세~55세</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(3,'56세이상');">56세이상</li>
            </ul>
          </center>
        </div>
      </div>
    </section>

    <!-- show(3) -->
    <section id="lovetest">
      <div class="join-charge">
        <div style="background-image: url('../../static/images/lovetest/ideal_find_test/mobile_bg01.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <div style="margin-left:20px; padding-top:25px;">
            <img src="../../static/images/lovetest/ideal_find_test/m_btn_prev.png" alt="" onclick="show(2);return false;" />
          </div>
          <div class="container" style="width: 300px; padding-top:10px;">
            <img class="progress-bar2" src="/new/image/ideal_find_test/heart.png"></img>
            <div class="progress border">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <p class="p_text">Q2. 내가 찾는 이상형 신장을 <br> 선택해 주세요</p>
          <center>
            <ul class="radio-box">
              <li class="list_box" onclick="show(4,'157cm이하');">157cm이하</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(4,'158~162cm');">158~162cm</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(4,'163cm~167cm');">163cm~167cm</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(4,'168~172cm');">168~172cm</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(4,'173cm~177cm');">173cm~177cm</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(4,'178~182cm');">178~182cm</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" style="width: 100%;" onclick="show(4,'183cm이상');">183cm이상</li>
            </ul>
          </center>
        </div>
      </div>
    </section>

    <!-- show(4) -->
    <section id="lovetest">
      <div class="join-charge">
        <div style="background-image: url('../../static/images/lovetest/ideal_find_test/mobile_bg01.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <div style="margin-left:20px; padding-top:25px;">
            <img src="../../static/images/lovetest/ideal_find_test/m_btn_prev.png" alt="" onclick="show(3);return false;" />
          </div>
          <div class="container" style="width: 300px; padding-top:10px;">
            <img class="progress-bar2" src="/new/image/ideal_find_test/heart.png"></img>
            <div class="progress border">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <p class="p_text">Q3. 내가 찾는 이상형 학력을 <br> 선택해주세요</p>
          <center>
            <ul class="radio-box">
              <li class="list_box" onclick="show(5,'고졸이상');">고졸이상</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(5,'대학(2,3년제)이상');">대학(2,3년)이상</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(5,'대졸이상');">대졸이상</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(5,'대학원이상');">대학원이상</li>
            </ul>
          </center>
        </div>
      </div>
    </section>

    <!-- show(5) -->
    <section id="lovetest">
      <div class="join-charge">
        <div style="background-image: url('../../static/images/lovetest/ideal_find_test/mobile_bg01.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <div style="margin-left:20px; padding-top:25px;">
            <img src="../../static/images/lovetest/ideal_find_test/m_btn_prev.png" alt="" onclick="show(4);return false;" />
          </div>
          <div class="container" style="width: 300px; padding-top:10px;">
            <img class="progress-bar2" src="/new/image/ideal_find_test/heart.png"></img>
            <div class="progress border">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <p class="p_text">Q4. 내가 찾는 이상형 직업군을 <br> 선택해주세요</p>
          <center>
            <ul class="radio-box">
              <li class="list_box" onclick="show(6,'무관');">무관</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(6,'사무,금융직');">사무,금융직</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(6,'기술,의료,언론');">기술,의료,언론</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(6,'교사,강사,공무원');">교사,강사,공무원</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(6,'자영업,사업,특수직');">자영업,사업,<br>특수직</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(6,'예능계,프리랜서,서비스');">예능계,프리랜서,서비스</li>
            </ul>
            <ul class="radio-box">
              <li class="list_box" onclick="show(6,'유학생,석박사');">유학생,석박사</li>&nbsp;&nbsp;
              <li class="list_box" onclick="show(6,'전문직기타');">전문직기타</li>
            </ul>
          </center>
        </div>
      </div>
    </section>

    <!-- show(6) 카카오로 결과 확인하기 -->
    <section id="lovetest">
      <div class="join-charge">
        <div style="background-image: url('../../static/images/lovetest/ideal_find_test/mobile_bg02.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <div style="margin-left:20px; padding-top:25px;">
            <img src="../../static/images/lovetest/ideal_find_test/m_btn_prev.png" alt="" onclick="show(5);return false;" />
          </div>
          <center>
            <div style="margin-top:450px;">
              <img style="width: 300px;" src="../../static/images/lovetest/ideal_find_test/btn_kakao.png" alt="" onclick="javascript:kakaoGetData();" />
            </div>
          </center>
        </div>
      </div>
    </section>

    <!-- show(7) result section -->
    <section id="lovetest">
      <div class="join-charge">
        <div style="background-image: url('../../static/images/lovetest/ideal_find_test/m_result.png'); height:600px; background-repeat: no-repeat; background-position:center;">
          <div style="margin-left:20px; padding-top:25px;">
            <img src="../../static/images/lovetest/ideal_find_test/m_btn_re.png" alt="" onclick="window.location.reload();" />
          </div>
        </div>
      </div>
    </section>


  </div>
  <!-- //컨텐츠 영역 -->
  <?
  require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/footer.php");
  ?>
</body>

</html>