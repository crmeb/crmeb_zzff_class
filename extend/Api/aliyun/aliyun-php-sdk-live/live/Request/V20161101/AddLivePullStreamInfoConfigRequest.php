<?php

namespace live\Request\V20161101;

class AddLivePullStreamInfoConfigRequest extends \RpcAcsRequest
{

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'live',
            '2016-11-01',
            'AddLivePullStreamInfoConfig',
            'live'
        );
    }

    /**
     * @param string $liveDomainType
     *
     * @return $this
     */
    public function setAppName($AppName)
    {
        $this->requestParameters['AppName'] = $AppName;
        $this->queryParameters['AppName'] = $AppName;

        return $this;
    }

    public function setDomainName($DomainName)
    {
        $this->requestParameters['DomainName'] = $DomainName;
        $this->queryParameters['DomainName'] = $DomainName;

        return $this;
    }

    public function setEndTime($EndTime)
    {
        $this->requestParameters['EndTime'] = $EndTime;
        $this->queryParameters['EndTime'] = $EndTime;

        return $this;
    }

    public function setSourceUrl($SourceUrl)
    {
        $this->requestParameters['SourceUrl'] = $SourceUrl;
        $this->queryParameters['SourceUrl'] = $SourceUrl;

        return $this;
    }

    public function setStartTime($StartTime)
    {
        $this->requestParameters['StartTime'] = $StartTime;
        $this->queryParameters['StartTime'] = $StartTime;

        return $this;
    }

    public function setStreamName($StreamName)
    {
        $this->requestParameters['StreamName'] = $StreamName;
        $this->queryParameters['StreamName'] = $StreamName;

        return $this;
    }
}