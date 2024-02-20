<?php

namespace APS;

use sample\test;

/**
 * 路由管理 \ 钩子管理
 * ASRoute
 *
 * 路由主要分析链接地址，进行参数化转换。 访问参数管理等。
 * 默认路由格式为: class/action/id 格式可以通过ASSetting进行设置,或自定义输入 eg: device/page/action/id
 *
 * @package APS\core
 */
class ASRoute extends ASObject{

    /**
     * 格式化路由
     * @var array|null;
     */
    public $route;

    /**
     * 路由格式
     * @var string|null
     */
    private $pathFormat;

    /**
     * 请求参数URL字符
     * @var string
     */
    public $querys;

    /**
     * 请求参数数组形式
     * @var null
     */
    public $params;

    /**
     * 输出模式
     * @var string
     */
    private $mode;

    /**
     * ASRoute constructor.
     * @param string|null $pathFormat 路由格式
     * @param string|null $mode 输出模式 ASRouteExportMode
     */
    function __construct( string $pathFormat = null, string $mode = null ){

        parent::__construct();

        $this->pathFormat = $pathFormat ?? 'class/action/id';
        $this->mode = $mode ?? ASAPI_Mode_ASAPI;

        $this->urlToRoute();

        $this->initParams();
    }

    /**
     * 全局单例
     * shared
     * @param  string|null  $format
     * @param  string|null  $mode
     * @return ASRoute
     */
    public static function shared( string $format = null, string $mode = null ):ASRoute{

        if ( !isset($GLOBALS['ASRoute']) ){
            $GLOBALS['ASRoute'] = new ASRoute( $format,$mode );
        }
        return $GLOBALS['ASRoute'];

    }

    public function setMode( string $mode ){
        $this->mode = $mode;
    }

    public function urlToRoute()
    {

        $path = $_GET['path'] ?? NULL;
        $webRouteTypes = explode('/',$this->pathFormat);
        $path = $path!='/' ? trim($path,'/') : null;
        $path = isset($path) && $path!=='' ?  explode( '/', $path ) : [];

        $rewritePath = [];

        if( !empty($path) ) {

            foreach ($webRouteTypes as $i => $key) {
                $rewritePath[$key] = $path[$i] ?? null;
            }
        }

        $this->route = $rewritePath;
    }

    public function initParams(){

        $INPUTS = json_decode(file_get_contents("php://input"),true) ?? []; # payload data
        $_INPUTS = array_merge($INPUTS,$_POST,$_GET);

        $this->params = $_INPUTS;
    }

    /**
     * 转换特殊参数到操作符
     * convertParams
     * @return void
     */
    public function convertParams(){

        if(isset($this->params['path'])){ unset($this->params['path']); }
        $this->querys = $this->buildQuery($this->params); # Querys 保持原文格式
        if( gettype($this->params)!=='array' ){return;}

        foreach ($this->params as $key => $value) {
            if( gettype($value) === 'string' && strstr($value,',')){
                if(!strstr($value, ']]')){
                    $this->params[$key] = "[[IN]]".$value;
                }
            }
        }
    }

    /**
     * 路由生成
     * buildRouteQuery
     * @param array|null $params
     * @return string
     */
    public function buildQuery( array $params = null ): string
    {

        $query = "?q";
        if( !isset($params) || empty($params)){ return $query.'=none'; }

        foreach ($params as $key => $value) {

            $query .= '&'.$key.'='.$value;
        }
        return $query;
    }

    /**
     * 静态加载文件
     * loadAPIFile
     * @param  string  $namespace
     * @param  string  $class
     */
    public static function loadAPIFile( string $namespace, string $class ){

        $fileName = Mixer::mix( ['namespace'=>$namespace,'class'=>$class], '{{namespace}}/{{class}}.php' );
        $filePath = API_DIR.'default/'.$fileName;
        $customFilePath = API_DIR.'custom/'.$fileName;

        if ( file_exists($customFilePath) ){
            include_once $customFilePath;
        }elseif ( file_exists($filePath) ){
            include_once $filePath;
        }else{
            _ASRoute()->exit( ASResult::shared(410,'File not found') );
        }
    }


    /**
     * 执行接口程序
     * run API
     * @param User|null  $user
     */
    public function runAPI( User $user = null ){

        $fileName = Mixer::mix( $this->route, '/{{namespace}}/{{class}}.php' );
        $filePath = API_DIR.'/default'.$fileName;
        $customFilePath = API_DIR.'/custom'.$fileName;

        if ( file_exists($customFilePath) ){
            include_once $customFilePath;
        }elseif ( file_exists($filePath) ){
            include_once $filePath;
        }else{
            $this->exit( $this->error(410,'File not found') );
        }

        $apiClass = '\\'.$this->route['namespace'].'\\'.$this->route['class'];
        $apiInstance = new $apiClass( $this->params, $user ) ?? new \sample\test();

        $this->mode   = $apiInstance->mode ?? $this->mode;
        $this->result = $apiInstance->runAPI();

        $this->export();
    }

    public function runTester( User $user ){

        $fileName = Mixer::mix( $this->route, '/{{namespace}}/{{class}}.php' );
        $filePath = TESTER_DIR.'/default'.$fileName;
        $customFilePath = TESTER_DIR.'/custom'.$fileName;

        if ( file_exists($customFilePath) ){
            include_once $customFilePath;
        }elseif ( file_exists($filePath) ){
            include_once $filePath;
        }else{
            $this->exit( $this->error(410,'File not found') );
        }

        $testClass = '\\'.$this->route['namespace'].'\\'.$this->route['class'];
        $testInstance = new $testClass( $this->params, $user ) ?? new \sample\test();

        $this->mode   = $testInstance->mode ?? $this->mode;
        $this->result = $testInstance->runTest();

        $this->export();
    }


    /**
     * 输出结果
     * export
     */
    protected function export(){

        if (!$this->result) {
            $this->GoodSay();
            exit();
        }

        header("ResponseTiming: ".(microtime(true) - TIME_START ));
        header("Engine: Powered by AppSite.cn");
        header("AppSite: PHP全栈开发引擎 Cross-Platform Development Engine");
        switch ($this->mode) {
            case ASAPI_Mode_RAW:
                header("Content-type: text/plain; charset=utf-8");
                print_r($this->result->getContent());
                break;

            case ASAPI_Mode_HTML:
                header("Content-type: text/html; charset=utf-8");
                echo $this->result->getContent();
                break;

            case 'json':
            case 'javascript':
            case ASAPI_Mode_API:
            case ASAPI_Mode_ASAPI:
            case ASAPI_Mode_Json:
            case ASAPI_Mode_Javascript:
                header("Content-type: application/json; charset=utf-8");
                echo $this->result->toString();
                break;

            default:
                break;
        }

        _ASDB()->close();
    }

    /**
     * 强制退出
     * exit forced
     * @param ASResult|null  $result
     */
    public function exit( ASResult $result = null ){

        $this->result = $result ?? ASResult::shared(-10,'Request rejected.','ASRoute->exit');
        $this->export();
        exit();
    }


    /**
     * 劝退非法请求
     * GoodSay
     * @param  bool  $returnString
     * @return string | void
     */
    public function GoodSay( bool $returnString = true ): string
    {

        $all = [

            "全部秘决只有两句话：不屈不挠，坚持到底。——陀思妥耶夫斯基 \nAll the secret only two words: perseverance, stick it out.",
            "人的一生就是进行尝试，尝试的越多，生活就越美好。——爱默生 \nPerson's life is to try, try, the more the more beautiful life.",
            "形式是一只金瓶，思想之花插人其内，便可流芳百世。——法朗士 \nForm is a golden urn, the flower of thought people inside, can be a fair death honors the whole life.",
            "在一切道德品质之中，善良的本性在世界上是最需要的。——罗素 \nAmong all moral qualities, good nature in the world is most in need.",
            "人生的光荣，不在永远不失败，而在于能够屡扑屡起。——拿破仑 \nThe glory of life, is not in never falling, but is the ability to repeatedly jump up.",
            "诚恳。不欺骗人；思想要纯洁公正；说话也要如此。——富兰克林 \nSincere. Not to deceive; Thoughts to pure justice; So to speak.",
            "人生应该如蜡烛一样，从顶燃到底，一直都是光明的。——萧楚女 \nLife should be like a candle, burning from the top to the end, is always bright.",
            "要不是我自己为自己建立纪念碑，这纪念碑，它从何而来？——歌德 \nIf it weren't for my own to build the monument, the monument, it come from?",
            "没有什么事是好的或坏的，但思想却使其中有所不同。——莎士比亚 \nNothing is good or bad, but thinking makes it different.",
            "人的思想如一口钟，容易停摆，需要经常上紧发条。——威赫兹里特 \nMan's thoughts such as a clock, easy lockout, requires constant wind.",
            "不要为已消尽之年华叹息，必须正视匆匆溜走的时光。——布莱希特 \nDon't have as time sigh, must face up to in a hurry the time slip away.",
            "坚持对于勇气，正如轮子对于杠杆，那是支点的永恒更新。——雨果 \nPersistence for courage, as the wheel for leverage, that is the pivot of eternal updates.",
            "只要持续地努力，不懈地奋斗，就没有征服不了的东西。——塞内加 \nAs long as the continuous efforts, unremitting struggle, no not things.",
            "普通人只想到如何度过时间，有才能的人设法利用时间。——叔本华 \nOrdinary people merely think how they shall spend their time; a man of talent tries to use it.",
            "一个人要表现最高的真诚，就必须做到无事不可对人言。——泰戈尔 \nA person to the highest sincerity, it must be have no matter to human speech.",
            "天空虽有乌云，但乌云的上面，永远会有太阳在照耀。——三浦绫子 \nAlthough there are dark clouds the sky, but the clouds above, there will be the sun shine forever.",
            "伟大变为可笑只有一步，但再走一步，可笑又会变为伟大。——佩思 \nGreat into a ridiculous is but a step, but the step again, and will become great.",
            "人生不是一种享乐，而是一桩十分沉重的工作。——列夫·托尔斯泰 \nLife is not a kind of enjoyment, but a pile is heavy work.",
            "只有恒心可以使你达到目的，只有博学可以使你明辨世事。——席勒 \nOnly persistence can make you achieve a goal, only knowledge can make you right from the world.",
            "要从容地着手去做一件事，但一旦开始，就要坚持到底。——比阿斯 \nLeisurely to start to do a thing, but once you begin, is going to stick it out.",
            "你不同情跌倒的人的痛苦，在你遇难时也将没有朋友帮忙。——萨迪 \nDon't you sympathize with the sufferings of a fall of man, will be without a friend to help when you were killed.",
            "具有才能的人总是善良的，坦白的，爽直的，决不矜持。——巴尔扎克 \nHas the talented person is always kind, frank, outgoing, never reserved.",
            "忍耐和坚持虽是痛苦的事情，但却能渐渐地为你带来好处。——奥维德 \nPatience and persistence are painful, but it can gradually bring you benefits.",
            "谦逊是反省的最高贵的收获，它建立起对抗骄傲的防线。——温刹斯基 \nHumility is the most noble self-examination harvest, it established a line of defense against pride.",
            "财富是猫的尾巴，只要勇往直前，财富就会悄悄跟在后面。——王志东 \nWealth is the cat's tail, as long as the courage, wealth will quietly follow behind.",
            "不要停顿，因为别人会超过你；不要返顾，以免摔倒。——阿·雷哈尼 \nDon't stop, because others will more than you; Don't return, so as not to fall.",
            "讨论犹如砺石，思想好比锋刃，两相砥栃将使思想更加锋利。——培根 \nDiscussion is like a burr, thought is like a blade, a two-phase strop 栃 will make the mind more sharp.",
            "时间是一个伟大的作者，它会给每个人写出完美的结局来。——卓别林 \nTime is a great author, it will give each person to write a perfect ending.",
            "卓越的人的一大优点是：在不利和艰难的遭遇里百折不挠。——贝多芬 \nA major advantage of the remarkable people is: perseverance in the adverse and difficult encounter.",
            "当一个人一心一意做好事情的时候，他最终是必然会成功的。——卢梭 \nWhen a person is undivided attention when doing things, he is bound to succeed.",
            "切莫垂头丧气，即使失去了一切，你还握有未来。——奥斯卡·王尔德 \nDon't depressed, even if lost everything, you still have the future.",
            "滴水穿石，不是因其力量，而是因其坚韧不拔、锲而不舍。——拉蒂默 \nConstant dropping wears away a stone, not because of its power, but because of its perseverance, perseverance.",
            "人生的价值，并不是用时间，而是用深度去衡量的。——列夫·托尔斯泰 \nThe value of life, not with the time, but with depth to measure.",
            "抛弃今天的人，不会有明天；而昨天，不过是行去流水。——约翰·洛克 \nAbandoned people today, there will be no tomorrow; And yesterday, but is flowing water line.",
            "环境影响人的成长，但它并不排斥意志的自由表现。——车尔尼雪夫斯基 \nEnvironment affect the person's growth, but it does not exclude free expression of will.",
            "我愿证明，凡是行为善良与高尚的人，定能因之而担当患难。——贝多芬 \nI would like to prove that every act of kindness and noble person, can so and bear their troubles.",
            "我有两个忠实的助手，一个是我的耐心，另一个就是我的双手。——蒙田 \nI have two faithful assistant, one is my patience, another is my hands.",
            "上天完全是为了坚强你的意志，才在道路上设下重重的障碍。——泰戈尔 \nGod fully is to strength your will, just set the heavy obstacles on the road.",
            "取得成就时坚持不懈，要比遭到失败时顽强不屈更重要。——拉罗什夫科 \nAchievement persistence, fortitude is more important than when failure.",
            "从不为艰难岁月哀叹，从不为自己命运悲伤的人，的确是伟人。——塞内加 \nNever for the difficult times, never mourn for their own destiny, is indeed a great man.",
            "意志是独一无二的个体所拥有的、以纠正自己的自动性的力量。——劳伦斯 \nWill is unique, to correct their own individual have the power of automaticity.",
            "人只有献身于社会，才能找出那短暂而有风险的生命的意义。——爱因斯坦 \nOnly dedicated to the society, to find out the short and has a risk of the meaning of life.",
            "只有刚强的人，才有神圣的意志，凡是战斗的人，才能取得胜利。——歌德 \nOnly the strong man, have a divine will, those who fight to win.",
            "一个人的价值，应该看他贡献什么，而不应当看他取得什么。——爱因斯坦 \nThe value of a person, should see him what contribution, should not look at what he achieved.",
            "良机对于懒惰没有用，但勤劳可以使最平常的机遇变良机。——马丁·路德 \nOpportunity for lazy useless, but diligence can make the most common opportunities.",
            "思想是天空中的鸟，在语言的笼里，也许会展翼，却不会飞翔。——纪伯伦 \nThought is a bird in the sky, in the language of a cage, perhaps a wing, but won't fly.",
            "顺境中的好运，为人们所希冀；逆境中的好运，则为人们所惊奇。——培根 \nIn the good luck, hope for people; Good luck in adversity, for people by surprise.",
            "要在这个世界上获得成功，就必须坚持到底：至死都不能放手。——伏尔泰 \nTo succeed in this world, we must stick to it: to death can't let go.",
            "痛苦留给的一切，请细加回味！苦难一经过去，苦难就变为甘美。——歌德 \nFor all the pain, please fine aftertaste! Suffering is the past, suffering becomes sweet.",
            "固然我有某些优点，而我自己最重视的优点，却是我的谦虚。——孟德斯鸠 \nIs I have certain advantages, and I own the advantage of the most important, it is my humble.",
            "只有把抱怨环境的心情，化为上进的力量，才是成功的保证。——罗曼·罗兰 \nOnly you turn your complaints of environments into the strength to strive for the better is the guarantee of success.",
            "任何时候做任何事，订最好的计划，尽最大的努力，作最坏的准备。——李想 \nAt any time to do anything, made the best plans, and do our best, prepare for the worst.",
            "充满着欢乐与斗争精神的人们，永远带着欢乐，欢迎雷霆与阳光。——赫胥黎 \nIs full of joy and fighting spirit of the people, and always with the joy, welcome thunder and sunshine.",
            "告诉你使我达到目标的奥秘吧，我唯一的力量就是我的坚持精神。——巴斯德 \nTell you the mystery of the enable me to reach the goal, my only strength is I adhere to the spirit.",
            "时间是个常数，但也是个变数。勤奋的人无穷多，懒惰的人无穷少。——字严 \nTime is a constant, but it is also a variable. Diligent people infinite, infinite less lazy people.",
            "人人都抱怨缺乏记忆力，但没有一个人抱怨缺乏健全的思想。——拉罗什富科 \nEveryone complained about the lack of memory, but no one complained about the lack of a sound mind.",
            "在寂寞无聊中，一个人才能感到跟关于思想的人在一起生活的好处。——卢梭 \nIn the lonely, a man can feel like about the benefits of thoughts of people live together.",
            "用感情生活的人的生命是悲剧，用思想生活的人的生命是喜剧。——布律耶尔 \nWith the emotional life of a man's life is a tragedy, by thought the life of someone whose life is a comedy.",
            "告诉你使我达到目标的奥秘吧，我惟一的力量就是我的坚持精神。——巴斯德 \nTell you the mystery of the enable me to reach the goal, the power of my only is my adhere to the spirit.",
            "春天不播种，夏天就不会生长，秋天就不能收割，冬天就不能品尝。——海德 \nWon't grow not sowing in spring, summer, autumn will not be able to harvest, the winter can't taste it.",
            "人的面孔常常反映他的内心世界，以为思想没有色彩，那是错误的。——雨果 \nPeople face often reflect his inner world, thought thought no color, it is wrong.",
            "谁要游戏人生，他就一事无成；谁不能主宰自己，永远是一个奴隶。——歌德 \nWho is going to the game of life, he accomplishes nothing; Who cannot control themselves, is always a slave.",
            "顺境的美德是节制，逆境的美德是坚韧，这后一种是较为伟大的德性。——培根 \nIn the virtues of moderation, adversity virtue is tenacious, the latter is a great virtue.",
            "世界上只有一种英雄主义，那就是了解生命而且热爱生命的人。——罗曼·罗兰 \nThere is only one heroism in the world, that is to understand life and love life. ",
            "我们越是忙越能强烈地感到我们是活着，越能意识到我们生命的存在。——康德 \nThe more busy the more can we felt strongly that we are alive, can realize the existence of our lives.",
            "思想必须以极端的方法才能进步，然而又必须以中庸之道才能延续。——瓦莱里 \nThought must be in extreme way to progress, however, must be in the middle to continue.",
            "人会长久停留在一个思想上，因而他也就有可能被束缚住手脚。——哈里法克斯 \nPeople will stay on a thought for a long time, so he could also be prisoners to it.",
            "谁能以深刻的内容充实每个瞬间，谁就是在无限地延长自己的生命。——库尔茨 \nWho can with profound content enrich every moment, who is in infinite to extend the life of their own.",
            "一个人再有本事，也得通过所在社会的主流价值认同，才能有机会。——任正非 \nA person no matter, also have to go through in the mainstream values of social identity, to have a chance.",
            "“神童”和“天才”，如果没有适当的环境和不断努力，就不能成才，甚至堕落为庸人。——维纳 ",
            "诚实而无知，是软弱的，无用的；然而有知识而不诚实，却是危险的，可怕的。——约翰逊 ",
            "人的思想是了不起的，只要专注于某一项事业，就一定会做出使自己感到吃惊的成绩。——马克·吐温 ",
            "太阳既不会夸大，也不会缩小，有什么就照出什么，是什么样子就照什么样子。——高尔基 ",
            "构成生命的主要成分，并非事实和事件，它主要的成分是思想的风暴，它一生一世都在人的脑中吹袭。——马克吐温 ",
            "请记住，环境愈艰难困苦，就愈需要坚定毅力和信心，而且，懈怠的害处也就愈大。——列夫·托尔斯泰 ",
            "生命里最重要的事情是要有个远大的目标，并借助才能与坚毅来完成它。——歌德 ",
            "只要有坚强的意志力，就自然而然地会有能耐、机灵和知识。——陀思妥耶夫斯基 ",
            "生活里没有做不到的事，但需要有强烈的愿望，必要时应该不惜生命。——列·列昂诺夫 ",
            "对我来说，一件尚未实现的事，就是我有生之年的最大鞭策。——埃尔温·怀特 ",
            "生活是一场艰苦的斗争，永远不能休息一下，要不然，你一寸一尺苦苦挣来的，就可能在一刹那间前功尽弃。——罗曼·罗兰 ",
            "如果意志要想具有法的权能，它就必须在理性发号施令时受理性的节制。——阿奎那 ",
            "人并不是只有一个圆心的圆圈；它是一个有两个焦点的椭圆形。事物是一个点，思想是另一个点。——雨果 ",
            "传播思想，无损于思想的传播者；同样，点燃蜡烛照亮他人者，也不会给自己带来黑暗。——杰弗逊 ",
            "达到重要目标有两个途径—努力及毅力。努力只有少数人所有，但坚韧不拔的毅力则多数人均可实行。——拿破仑 ",
            "个人的活动，如果不是被高尚的思想所鼓舞，那它是无益的、渺小的。——车尔尼雪夫斯基 ",
            "在希望与失望的决斗中，如果你用勇气与坚决的双手紧握着，胜利必属于希望。——普里尼 ",
            "人，总有根据前人思索过的记忆来使用眼睛的习惯，因而一切东西都一定还有未被探索到的地方。——福楼拜 ",
            "不要心平气和，不要容你自己昏睡！趁你还年轻，强壮、灵活，要永不疲倦地做好事。——契诃夫 ",
            "只有经过地狱般的磨练，才能炼出创造天堂的力量、只有流过血的手指，才能弹奏出世间的绝唱。——泰戈尔 ",
            "蜜蜂从花中啜蜜离开时营营地道谢，浮夸的蝴蝶却是相信花是应该向他道谢的。——泰戈尔 ",
            "善良的行为有一种好处，就是使人的灵魂变得高尚了，并且使它可以做出更美好的行为。——卢梭 ",
            "思想寓于躯体，但尽管如此，身体最健壮的人不一定就是杰出的思想家。——伏尔泰 ",
            "事情是很简单的人，全部秘诀只有两句话：不屈不挠，坚持以底。——陀思妥耶夫斯基 ",
            "时间是最公开合理的，它从不多给谁一份，勤劳者能叫时间留给串串的果实，懒惰者时间给予他们一头白发，两手空空。——高尔基 ",
            "即使在把眼睛盯着大地的时候，那超群的目光仍然保持着凝视太阳的能力。——雨果 ",
            "逆境有一种科学价值。一个好的学者是不会放弃这种机会来学习的。——爱默生 ",
            "“是”和“否”是最古老最简单的两个词，可是它们需要人们最多的思考。——毕达哥斯拉 ",
            "一个训练有素的思想家的主要特点在于，他不在佐证不足的情况下轻易做出结论。——贝弗里奇 ",
            "一个人在受到责备而不是受到赞扬之后仍能保持谦虚，那才是真正的谦虚。——里克特 ",
            "一个崇高的目标，只要不渝地追求，就会居为壮举；在它纯洁的目光里，一切美德必将胜利。——华兹华斯 ",
            "很少见到有人专心致志地去完成一件美好而正当的事。我们通常见到的，不是畏首畏尾的学究，就是急于求成的莽汉。——歌德 ",
            "集体的习惯，其力量更大于个人的习惯。因此如果有一个有良好道德风气的社会环境，是最有利于培训好的社会公民的。——培根 ",
            "懒惰和贫穷永远是丢脸的，所以每个人都会尽最大努力去对别人隐瞒财产，对自己隐瞒懒惰。——塞缪尔·约翰逊 ",
            "人在意志力和斗争性方面的长处或短处，往往是导致他们成功或失败的重要原因之一。——哈代 ",
            "人在逆境里比在顾境里更能坚强不屈，遭厄运时比交好运时更容易保全身心。——雨果 ",
            "上帝的恩惠像一支蜡烛，人的意志像制蜡烛的蜡，人要登上炼狱山顶的上地上乐园，也缺少不得自己的意志。——但丁 ",
            "请记住，环境愈艰难困苦，就愈需要坚定毅力和信心，而且，懈怠的害处也就愈大。——托尔斯泰 ",
            "有困难是坏事也是好事，困难会逼着人想办法，困难环境能锻炼出人才来。——徐特立 ",
            "克服自己消极的钻牛角尖的扭曲的思想方式，便能增加效率，提高自尊心。——伯恩斯 ",
            "人的意志并不总是万能的，因为笑声和泪水会随着那产生这些东西的激情接踵而来，最真诚的人最不能控制它们。——但丁 ",
            "时间是世界上一切成就的土壤。时间给空想者痛苦，给创造者幸福。——麦金西 ",
            "只要有一种无穷的自信充满了心灵，再凭着坚强的意志和独立不羁的才智，总有一天会成功的。——莫泊桑 ",
            "世间大部分的贫穷，都是一种病态，是不良生活、不良环境、不良思想的结果。——萨克斯 ",
            "平静的湖面，炼不出精悍的水手；安逸的环境，造不出时代的伟人。——列别捷夫 ",
            "意志的出现不是对愿望的否定，而是把愿望合并和提升到一个更高的意识水平上。——罗洛·梅 ",
            "人生恰恰像马拉松赛跑一样，有坚持到最后的人，才能称为胜利者。——池田大作 ",
            "要支配自己的思想？你如果不做它的主人，就会成为它的奴仆。去用噘子和铁链驾驭它吧！——贺拉斯 ",
            "如果你很有天赋，勤勉会使天赋更加完善；如果你的才能平平，勤勉会补足缺陷。——雷诺兹 ",
            "真正的蒙味主义并不去阻止传播真实的、明白的和有用的事物，而是使假的东西到处流行。——歌德 ",
            "同情，使软弱的人觉得这个世界温柔；使坚强的人觉得这个世界高尚。——阿诺德 ",
            "国王能够抵挡住人民的斗争，但如果人民开始思考，他就维持不了多久。——雨果 ",
            "我们应有恒心，尤其要有自信心！我们必须相信，我们的天赋是要用来做某种事情的。——居里夫人 ",
            "一个人围着一件事转，最后全世界可能都会围着你转；一个人围着全世界转，最后全世界可能都会抛弃你。——刘东华 ",
            "按照自己的意志去做，不要听那些闲言碎语，你就一定会成功。——纳斯雷丹·霍查 ",
            "懒惰，像生锈一样，比操劳理能消耗身体；经常用的钥匙总是亮闪闪的。——富兰克林 ",
            "意志是自由自在的，人实现了他的意志，也等于实现了他自己，而这种自我实现对个人来说是一种最大的满足。——弗洛姆 ",
            "要是一个人，能充满信心地朝他理想的方向去做，下定决心过他所想过的生活，他就一定会得到意外的成功。——戴尔·卡内基 ",
            "有百折不挠的信念的所支持的人的意志，比那些似乎是无敌的物质力量有更强大的威力。——爱因斯坦 ",
            "要永远相信：当所有人都冲进去的时候赶紧出来，所有人都不玩了再冲进去。——李嘉诚 ",
            "当一个人不仅对别人、甚至对自己都不会有一丝欺骗的时候，他的这种特性就是真挚。——柯罗连科 ",
            "内容充实的生命就是长久的生命。我们要以行为而不是以时间来衡量生命。——小塞涅卡 ",
            "思而后行，以免做出蠢事。因为草率的动作和言语，均是卑劣的特征。——毕达哥拉斯 ",
            "我忍耐地回想或思考任何悬而不决的问题，甚至连费数年也在所不惜。——达尔文 ",
            "你们应该培养对自己，对自己的力量的信心，百这种信心是靠克服障碍，培养意志和锻炼意志而获得的。——高尔基 ",
            "要记住！情况越严重，越困难，就越需要坚定、积极、果敢，而越无为就越有害。——列夫·托尔斯泰 ",
            "无论什么时候，不管遇到什么情况，我绝不允许自己有一点点灰心丧气。——爱迪生 ",
            "智慧是命运的一部分，一个人所遭遇的外界环境是会影响他的头脑的。——莎士比亚 ",
            "世界上只有两种力量，一种是剑，一种是思想，而思想最终总是战胜剑。——拿破仑 ",
            "在奔向目标的道理上支持不懈持之以恒，充分意识到自己的力量。——陀思妥耶夫斯基 ",
            "无论大事还是小事，只要自己是认为办得好的，就坚定地去办，这就是性格。——歌德 ",
            "人，只要有一种信念，有所追求，什么艰苦都能忍受，什么环境也都能适应。——丁玲 ",
            "在人生的大风浪中，我们常常学船长的样子，在狂风暴雨之下把笨重的货物扔掉，以减轻船的重量。——巴尔扎克 ",
            "任凭怎样脆弱的人，只要把全部的精力倾注在唯一的目的上，必能使之有所成就。——西塞罗 ",
            "既然我已经踏上这条道路，那么，任何东西都不应妨碍我沿着这条路走下去。——康德 ",
            "能赢得普遍尊敬的人，并不是由于他显赫的地位，而是由于始终如一的言行和不屈不挠的精神。——列夫·托尔斯泰 ",
            "无论是美女的歌声，还是鬣狗的狂吠，无论是鳄鱼的眼泪，还是恶狼的嚎叫，都不会使我动摇。——恰普曼 ",
            "人的一生可能燃烧也可能腐朽，我不能腐朽，我愿意燃烧起来！——奥斯特洛夫斯基 ",
            "若是一个人的思想不能比飞鸟上升得更高，那就是一种卑不足道的思想。——莎士比亚 ",
            "累累的创伤，就是生命给你的最好的东西，因为在每个创伤上在都标示着前进的一步。——罗曼·罗兰 ",
            "在科学上没有平坦的大道，只有不畏劳苦，沿着陡峭山路攀登的人，才有希望达到光辉的顶点。——马克思 ",
            "每一点滴的进展都是缓慢而艰巨的，一个人一次只能着手解决一项有限的目标。——贝费里奇 ",
            "开创伟大事业的是天才，完成伟大事业的是辛苦。勉之期不止，多获由力耘。——欧阳修 ",
            "一般都认为幸福存在于闲暇。不管怎么说，我们为争取闲暇而工作，为生活在和平环境而战争。——亚里斯多德 ",
            "等待的方法有两种：一种是什么事也不做空等，一种是一边等一边把事业向前推动。——屠格涅夫 ",
            "在月球遥望地球，我看不到任何国界，我觉得地球就是一个整体，我的整个思想也就开阔了。——塞尔南 ",
            "不作什么决定的意志不是现实的意志；无性格的人从来不做出决定。——黑格尔 ",
            "深刻的思想就像铁钉，一旦钉在脑子里，什么东西也没法把它拔出来。——狄德罗 "

        ];

        $say = $all[rand(0,count($all)-1)];
        if ($returnString) {
            return $say;
        }else{
            $this->result->setContent( $say );
            $this->export();
            return '';
        }
    }


}